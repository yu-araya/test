<ul id="breadcrumbs">
	<li><a>パスワード変更</a></li>
</ul>
<br>
<br>
<p><b><font size="4" color="blue"> 新しいパスワードを入力してください</font></b></p>
<?php
	if($this->getRequest()->getSession()->check('Message.auth')) {
		$this->Flash->render('auth');
	}
	echo $this->Form->create(null, array('type' => 'post', 'url' => array('controller' => 'administrators', 'action' => 'savePassword'), 'data-cy' => 'changePassWord'));
?><br>

<p>ID：<?php echo strval($this->getRequest()->getSession()->read('Auth.User.login_name')); ?></p>
<br>

<div class="input-table">
<table>
	<tr>
		<td style="width: 15%;">パスワード</td>
		<td>
			<?php echo $this->Form->control("Administrator[new_password1]", array('type' => 'password', 'label' => '',
			'size' => 25, 'maxlength' => 10, 'autocomplete' => 'off', 'data-cy' => 'inPassWord')); ?>
		</td>
	</tr>
	<tr>
		<td style="width: 15%;">パスワード再入力</td>
		<td>
			<?php echo $this->Form->control("Administrator[new_password2]", array('type' => 'password', 'label' => '',
			'size' => 25, 'maxlength' => 10, 'autocomplete' => 'off', 'data-cy' => 'againPassWord')); ?>
		</td>
	</tr>
</table><br>
</div>
<?php
	echo $this->Form->hidden("Administrator[login_name]", array('value' => $this->getRequest()->getSession()->read('Auth.User.login_name')));
	//echo $this->Form->end('変更');
	echo $this->Form->submit('変更', ['label' => false]);
	echo $this->Form->end();
?>
