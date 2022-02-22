<style type="text/css">
	#main_menu {display:none}
	#logout {display:none}
	.header {display:none}
</style>
<div class="login-info-area">
	<p><b><font size="4" color="blue"> ID　パスワードを入力してください</font></b></p>
	<?php
		if($this->getRequest()->getSession()->check('Message.auth')) {
			$this->Flash->render('auth');
		}
			echo $this->Form->create(null, array('type' => 'post', 'url' => array('controller' => 'administrators', 'action' => 'login')));
	?>
	<div class="input-table">
	<table>
		<tr>
			<td>ID</td>
			<td>
				<?php echo $this->Form->control('login_name', array('type' => 'text', 'label' => '', 'name' => "login_name", 'size' => 25, 'maxlength' => 10, 'id' => 'AdministratorLoginName')); ?>
			</td>
		</tr>
		<tr>
			<td>パスワード</td>
			<td>
				<?php echo $this->Form->control('password', array('type' => 'password', 'label' => '', 'name' => "password", 'size' => 25, 'maxlength' => 10, 'autocomplete' => 'off', 'id' => 'AdministratorPassword')); ?>
			</td>
		</tr>
	</table>
	</div>
	<?php
			echo $this->Form->submit('ログイン', ['label' => false]);
	?>
	<?php
		echo $this->Form->end();
	?>
</div>