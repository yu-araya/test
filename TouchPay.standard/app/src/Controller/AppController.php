<?php
/**
 * Application level Controller.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * @see          https://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 0.2.9
 *
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

//use App\Controller\AppController;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Xml;
//use Cake\ORM\Locator;

//App::import('Vendor', 'PHPExcel', array('file' => 'Excel' . DS . 'PHPExcel.php'));
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_NumberFormat;

/**
 * Application Controller.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @see		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    const MENU_BASE = 10;

    //   public $layout = 'admin_default';
    //   public $components = array(
//        'Auth' => array(
//           'loginAction' => array('controller' => 'Administrators', 'action' => 'login'),
//            'authenticate' => array(
//                'Form' => array(
//                  'userModel' => 'Administrators',
//                    'passwordHasher' => array(
//                        'className' => 'None'
//                    ),
//                    'fields' => array(
//                        'username' => 'login_name',
//                        'password' => 'password',
//                    ),
//                ),
//            )
//       ),
//    );
//        'DebugKit.Toolbar',

    // プロパティファイルデータ
    public $property = [];

    public function initialize()
    {
        parent::initialize();
        // CakePHP3ではSessionComponentが廃止の対応
        $this->Session = $this->getRequest()->getSession();
        $this->data = $this->getRequest()->getData();
        $this->loadComponent('Flash');
        $this->loadComponent(
        'Auth', [
                    'loginAction' => [
                        'controller' => 'Administrators',
                        'action' => 'login',
                    ],
                    'loginRedirect' => [
                        'controller' => 'Administrators',
                        'action' => 'index',
                    ],
                  'logoutRedirect' => [
                        'controller' => 'Administrators',
                        'action' => 'login',
                    ],
                    'authenticate' => [
                        'NoHash' => [
                                'userModel' => 'Administrators',
                                'fields' => ['username' => 'login_name', 'password' => 'password'],
                    'passwordHasher' => ['className' => 'None'],
                ],
            ],
        ]
    );
        $this->loadComponent('PaginatorForPdo');

        // Model呼び出し
        $this->Administrator = TableRegistry::getTableLocator()->get('Administrators');
        $this->AppSetting = TableRegistry::getTableLocator()->get('AppSettings');
        $this->Category = TableRegistry::getTableLocator()->get('Categories');
        $this->ContentsSetVersion = TableRegistry::getTableLocator()->get('ContentsSetVersions');
        $this->DayOffCalendar = TableRegistry::getTableLocator()->get('DayOffCalendars');
        $this->EmployeeInfo = TableRegistry::getTableLocator()->get('EmployeeInfos');
        $this->EmployeeKbn = TableRegistry::getTableLocator()->get('EmployeeKbns');
        $this->FoodDivision = TableRegistry::getTableLocator()->get('FoodDivisions');
        $this->FoodHistoryInfo = TableRegistry::getTableLocator()->get('FoodHistoryInfos');
        $this->FoodPeriod = TableRegistry::getTableLocator()->get('FoodPeriods');
        $this->InstrumentDivision = TableRegistry::getTableLocator()->get('InstrumentDivisions');
        $this->LoginHistory = TableRegistry::getTableLocator()->get('LoginHistorys');
        $this->Option = TableRegistry::getTableLocator()->get('Options');
        $this->RegistError = TableRegistry::getTableLocator()->get('RegistErrors');
        $this->ReservationDecision = TableRegistry::getTableLocator()->get('ReservationDecisions');
        $this->ReservationInfo = TableRegistry::getTableLocator()->get('ReservationInfos');
        $this->Tab = TableRegistry::getTableLocator()->get('Tabs');
        $this->FoodHistoryReservation = TableRegistry::getTableLocator()->get('FoodHistoryReservations');
        // エラー画面対応
        $this->viewBuilder()->setLayout('admin_default');
    }

    public function beforeFilter(Event $event)
    {
        $this->getProperty();

        // 認証除外ページ（ピットタッチ用ページ）
        $this->Auth->allow('proc');
        $this->Auth->allow('contentsUpdate');

        $this->loadComponent('S3Client');

        $this->set('message', $this->property['message']);
        $this->set('baseKbnName', $this->getCanReservationInstrumentList());
    }

    /**
     * 予約食事区分が存在する機器区分を取得.
     */
    private function getCanReservationInstrumentList()
    {
        $result = [];
        $instrumentList = $this->getInstrumentDivisionList();
//        $foodDivision = TableRegistry::get('FoodDivisions');
        $this->FoodDivision = $this->FoodDivision ? $this->FoodDivision : TableRegistry::getTableLocator()->get('FoodDivisions');
        $foodDivision = $this->FoodDivision;
        foreach ($instrumentList as $key => $value) {
            $foodDivisionList = $foodDivision->getFoodDivisionList($key, true);
            if (!empty($foodDivisionList)) {
                $result[$key] = $value;
                //		array_push($result[$key] = $value);
            }
        }

        return $result;
    }

    /**
     * プロパティファイルの読込
     */
    public function getProperty()
    {
        // プロパティファイルを読込む
//        $xml = simplexml_load_file(dirname(__FILE__).'/../config/property.xml');
        $xml = simplexml_load_string(file_get_contents(dirname(__FILE__).'/../../config/property.xml'));

        // 親の要素数分処理を行う
        foreach ($xml as $items) {
            $array = [];

            // 子の要素数分処理を行う
            foreach ($items as $item) {
                // 属性を取得
                $list = (array) $item->attributes();
                $list = $list['@attributes'];
                // リストにidとvalue値を追加
                $array += [strval($list['id']) => strval($list['value'])];
            }

            // 親要素に反映
            $this->property += [$items->getName() => $array];
        }
    }

    public function getPropertyArray()
    {
        $this->getProperty();

        return $this->property;
    }

    /**
     * 社員区分リストを抽出.
     */
    public function getConditionEmployeeKbnList()
    {
        $employeeKbnArray = [];

        $list = $this->getEmployeeKbnList();
        foreach ($list as $key => $value) {
            $employeeKbnArray[] = strval($key);
        }

        return $employeeKbnArray;
    }

    /**
     * メニューのカレント色の設定.
     *
     * @param $currentNumber メニュー番号
     */
    public function getMenuLink($currentNumber)
    {
        $header = [];

        for ($i = 0; $i < 20; ++$i) {
            if ($i == $currentNumber) {
                array_push($header, 'Current');
            } else {
                array_push($header, '');
            }
        }

        return $header;
    }

    /**
     * SJISの場合のみ文字コード変換.
     */
    public function convertEncode($str = null)
    {
        $to_encoding = 'UTF-8';
        if (mb_detect_encoding($str, $to_encoding, true) == false) {
            $str = mb_convert_encoding($str, $to_encoding, 'SJIS-win');
        }

        return $str;
    }

    /**
     * 日付チェック関数.
     *
     * @param date 日付
     */
    public function isDate($date)
    {
        $isValidDate = true;

        if (!preg_match('/\A[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}\z/', $date)) {
            $isValidDate = false;
        }
        list($year, $month, $date) = explode('-', $date);
        if (!checkdate($month, $date, $year)) {
            $isValidDate = false;
        }

        return $isValidDate;
    }

    /**
     * 日付文字列をYYYY-MM-DDに変換する.
     */
    public function formatToDateStr($date)
    {
        return PHPExcel_Style_NumberFormat::toFormattedString($date, PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
    }

    /**
     * 時刻チェック関数.
     */
    public function isTime($time)
    {
        if (mb_substr_count($time, ':') != 1) {
            return;
        }

        if (!preg_match('/\A[0-9]{2}:[0-9]{2}\z/', $time)) {
            return false;
        }

        list($h, $m) = explode(':', $time);

        // 桁数
        if (!(strlen($h) == 2 && strlen($m) == 2)) {
            return false;
        }

        // 妥当性
        if (!(intval($h) >= 0 && intval($h) < 24)) {
            return false;
        }
        if (!(intval($m) >= 0 && intval($m) < 60)) {
            return false;
        }

        return true;
    }

    /**
     * 食事区分リストを取得.
     *
     * @param $baseKbn 拠点区分
     * @param $actualOnly 実績のみ（true：実績のみ、falseまたは未指定：実績＋予約）
     */
    public function getFoodDivisionList($baseKbn = null, $actualOnly = false)
    {
        $conditions = ['delete_flg' => '0'];

        $condArray = array_merge([], $this->getFoodDivisionArrayFrom($baseKbn));
        if (!$actualOnly) {
            $condArray = array_merge($condArray, $this->getFoodDivisionArrayFrom($baseKbn, true));
        }
        if (count($condArray) > 0) {
            $conditions += ['food_division IN' => $condArray];
        }
        $foodDivisionList = $this->FoodDivision->findEX('all', [
            'conditions' => $conditions,
            'order' => 'food_division',
        ]);

        return Hash::Combine($foodDivisionList, '{n}.FoodDivision.food_division', '{n}.FoodDivision.food_division_name');
    }

    /**
     * 食事区分（予約のみ）リストを取得.
     *
     * @param $baseKbn 拠点区分
     */
    public function getFoodDivisionReservationList($baseKbn = null)
    {
        $conditions = ['delete_flg' => '0'];

        $foodDivisions = $this->getFoodDivisionArrayFrom($baseKbn, true);
        if (count($foodDivisions) > 0) {
            $conditions += ['food_division IN' => $foodDivisions];
        }

        $foodDivisionList = $this->FoodDivision->findEX('all', [
            'conditions' => $conditions,
            'order' => 'food_division',
        ]);
        //return Set::Combine($foodDivisionList, '{n}.FoodDivision.food_division', '{n}.FoodDivision.food_division_name');
        return Hash::Combine($foodDivisionList, '{n}.FoodDivision.food_division', '{n}.FoodDivision.food_division_name');
    }

    /**
     * 機器区分リストを取得.
     */
    public function getInstrumentDivisionList()
    {
        //$instrumentDivision = $this->InstrumentDivision ? $this->InstrumentDivision : ClassRegistry::init('InstrumentDivision');
        $instrumentDivision = $this->InstrumentDivision ? $this->InstrumentDivision : TableRegistry::getTableLocator()->get('InstrumentDivisions');
        $instrumentDivisionList = $instrumentDivision->findEX('all', [
            'conditions' => ['delete_flg' => '0'],
            'order' => 'instrument_division',
        ]);
        //return Set::Combine($instrumentDivisionList, '{n}.InstrumentDivision.instrument_division', '{n}.InstrumentDivision.instrument_name');
        return Hash::Combine($instrumentDivisionList, '{n}.InstrumentDivision.instrument_division', '{n}.InstrumentDivision.instrument_name');
    }

    /**
     * 社員区分リストを取得.
     */
    public function getEmployeeKbnList()
    {
        $employeeKbnList = $this->EmployeeKbn->findEX('all', [
            'conditions' => ['delete_flg' => '0'],
            'order' => 'LPAD(employee_kbn, 2, 0)',
        ]);
        //return Set::Combine($employeeKbnList, '{n}.EmployeeKbn.employee_kbn', '{n}.EmployeeKbn.employee_kbn_name');
        return Hash::Combine($employeeKbnList, '{n}.EmployeeKbn.employee_kbn', '{n}.EmployeeKbn.employee_kbn_name');
    }

    /**
     * カレンダーの配列を作成.
     */
    public function getCalendar($year, $month)
    {
        // 月末日を取得
        $lastDay = date('j', mktime(0, 0, 0, $month + 1, 0, $year));

        $calendar = [];
        $j = 0;

        // 月末日までループ
        for ($i = 1; $i < $lastDay + 1; ++$i) {
            // 曜日を取得
            $week = date('w', mktime(0, 0, 0, $month, $i, $year));

            // 1日の場合
            if ($i == 1) {
                // 1日目の曜日までをループ
                for ($s = 1; $s <= $week; ++$s) {
                    // 前半に空文字をセット
                    $calendar[$j]['day'] = '';
                    ++$j;
                }
            }

            // 配列に日付をセット
            $calendar[$j]['day'] = $i;
            ++$j;

            // 月末日の場合
            if ($i == $lastDay) {
                // 月末日から残りをループ
                for ($e = 1; $e <= 6 - $week; ++$e) {
                    // 後半に空文字をセット
                    $calendar[$j]['day'] = '';
                    ++$j;
                }
            }
        }

        return $calendar;
    }

    /**
     * 拠点区分から食事区分を決定する.
     */
    public function getFoodDivision($baseKbn)
    {
        switch ($baseKbn) {
            case '1': // 本社
                    $foodDivision = '1'; // 定食(予約)
                    //$foodDivision = '6'; // 丼(予約)
                break;
            case '2': // 美濃
                    $foodDivision = '2'; // 定食(予約)
                    //$foodDivision = '12'; // 丼(予約)
                break;
        }

        return $foodDivision;
    }

    /**
     * 拠点区分から予約可能な食事区分リストを返却する。
     */
    public function getReservationFoodDivisionList($baseKbn)
    {
        return $this->getFoodDivisionArrayFrom($baseKbn, true);
    }

    /**
     * 食事区分から拠点を取得（予約の食事区分から）.
     */
    public function getBaseKbnFoodDivision($foodDivision)
    {
        foreach (array_keys($this->getInstrumentDivisionList()) as $baseKbn) {
            if (in_array($foodDivision, $this->getFoodDivisionArrayFrom($baseKbn, true))) {
                return $baseKbn;
            }
        }

        return '';
    }

    /**
     * 食事区分リストを返します。
     *
     * @param string $base      拠点
     * @param bool   $isReserve 予約
     */
    protected function getFoodDivisionArrayFrom($base, $isReserve = false)
    {
        //$foodDivision = $this->FoodDivision ? $this->FoodDivision : TableRegistry::get('FoodDivisions');
        $this->FoodDivision = $this->FoodDivision ? $this->FoodDivision : TableRegistry::getTableLocator()->get('FoodDivisions');
        $foodDivision = $this->FoodDivision;

        return $foodDivision->getFoodDivisionList($base, $isReserve);
    }

    // 予約の食事区分のリストを返却
    protected function getreservationDivisionList()
    {
//        $foodDivision = TableRegistry::get('FoodDivisions');
        $this->FoodDivision = $this->FoodDivision ? $this->FoodDivision : TableRegistry::getTableLocator()->get('FoodDivisions');
        $foodDivision = $this->FoodDivision;
        $result = $foodDivision->findEX('all', [
            'feilds' => [
                'food_division',
                'food_division_name',
            ],
            'conditions' => [
                'NOT' => [
                    'reserve_food_division' => '0',
                ],
            ],
        ]);

        return !empty($result) ? Hash::Combine($result, '{n}.FoodDivision.food_division', '{n}.FoodDivision.food_division_name') : null;
    }

    /**
     * {@inheritdoc}
     *
     * @see Controller::beforeRender()
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        $this->set('property', $this->property);
        $foodDivisionToBase = [];
        foreach ($this->getInstrumentDivisionList() as $base => $baseName) {
            foreach ($this->getFoodDivisionArrayFrom($base) as $foodDivision) {
                $foodDivisionToBase[$foodDivision] = $baseName;
            }
            foreach ($this->getFoodDivisionArrayFrom($base, true) as $foodDivision) {
                $foodDivisionToBase[$foodDivision] = $baseName;
            }
        }
        $this->set('foodDivisionToBase', $foodDivisionToBase);
        $this->set('menuBase', AppController::MENU_BASE);
    }

    /**
     * コンテンツセットのバージョンが最新のバージョンであるかを確認する
     * 最新のバージョンと同じ or 最新のバージョンより高い場合はtrue
     * それ以外はfalse.
     *
     * param1 $checkTargetVersion チェック対象のバージョン
     * param2 $checkTargetRevision チェック対象のリビジョン
     * param3 $contentsType チェック対象のコンテンツセットのタイプ('E'-喫食管理, 'R'-予約管理)
     */
    public function isContentsSetVersionLatest($checkTargetVersion, $checkTargetRevision, $contentsType)
    {
        // php標準バージョン形式(x.yy -> x.y.y)に変更
        $checkTargetVersion = substr_replace($checkTargetVersion, '.', 3, 0);
        $latestVersion = substr_replace(explode('rev', $this->property['latest_contents_set_version'][$contentsType])[0], '.', 3, 0);

        // 最新リビジョン
        $latestRevision = explode('rev', $this->property['latest_contents_set_version'][$contentsType])[1];

        return version_compare($checkTargetVersion, $latestVersion, '>')
            || (version_compare($checkTargetVersion, $latestVersion, '=') && intval($checkTargetRevision) >= intval($latestRevision));
    }

    public function debuglog($str)
    {
        $this->log($str, 'debug');
    }

    //禁則文字チェック関数（チェック項目：、カンマ　’シングルコーテーション　”ダブルコーテーション）
    public function checkStopWord($checkword)
    {
        //タグの除去（不正スクリプト対策）
        $checkmsg = strip_tags($checkword);

        //配列を用い禁止語を作成する（※１）
        $stop_word = ["'", '"', ','];

        //禁止語の数を数える
        $num = count($stop_word);

        //禁止後の（配列の）数ループ
        for ($i = 0; $i < $num; ++$i) {
            //メッセージに禁止語が入っていないか調べる(※３)
            if (stristr($checkmsg, rtrim($stop_word[$i])) == true) {
                //禁則文字ありの場合
                return true;
            }
        }
    }

    /**
     * ファイルの拡張子が".csv"であるかどうかのチェック.
     *
     * @param ファイル名
     *
     * @return true:CSVです false:CSVではありません
     */
    public function isCsv($fileName)
    {
        // 拡張子の取得
        $extension = substr($fileName, strpos($fileName, '.') + 1);

        // 拡張子がCSVであるかのチェック
        if (strcasecmp($extension, 'csv') == 0) {
            return true;
        }

        return false;
    }

    /**
     * ファイルの拡張子が".xlsx"であるかどうかのチェック.
     *
     * @param ファイル名
     * @param 拡張子
     *
     * @return true false
     */
    public function isExcel($fileName)
    {
        // 拡張子の取得
        $extension = substr($fileName, strpos($fileName, '.') + 1);

        // 拡張子がxlsx,xlsであるかのチェック
        if (in_array($extension, ['xlsx', 'xls'])) {
            return true;
        }

        return false;
    }

    /**
     * ファイル名渡したら配列返すラッパー関数.
     *
     * @param ファイル名
     */
    public function readXlsx($readFile)
    {
        // xlsxをPHPExcelに食わせる
        $objPExcel = PHPExcel_IOFactory::load($readFile);
        // 配列形式で返す
        return $objPExcel->getActiveSheet()->toArray(null, true, false, false);
    }

    /**
     * /var/www/log/の下のディレクトリ名を返却.
     * demoの場合demo/{ディレクトリ名}
     * となる.
     *
     * @return void
     */
    public function getDirectoryName()
    {
        $urlArray = explode('/', Router::url());

        return $urlArray[1] !== 'demo' ? $urlArray[1] : "demo/$urlArray[2]";
    }

    /**
     * S3からメニューを取得する.
     */
    public function getFilesFromS3()
    {
        $directoryName = $this->getDirectoryName();
        $this->S3Client->getDirectory($directoryName, 'menu');
    }
}
