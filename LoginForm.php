<?php
/**
 * This file is part of the DreamFactory Services Platform(tm) (DSP)
 *
 * DreamFactory Services Platform(tm) <http://github.com/dreamfactorysoftware/dsp-core>
 * Copyright 2012-2013 DreamFactory Software, Inc. <developer-support@dreamfactory.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
use DreamFactory\Platform\Yii\Components\PlatformUserIdentity;
use DreamFactory\Yii\Utility\Pii;

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'WebController'.
 */
class LoginForm extends CFormModel
{
	/**
	 * @var string
	 */
	public $username;
	/**
	 * @var string
	 */
	public $password;
	/**
	 * @var boolean
	 */
	public $rememberMe;
	/**
	 * @var PlatformUserIdentity
	 */
	protected $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array( 'username, password', 'required' ),
			array( 'rememberMe', 'boolean' ),
			array( 'password', 'authenticate' ),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'   => 'Email Address',
			'rememberMe' => 'Keep me logged in',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate( $attribute, $params )
	{
		if ( !$this->hasErrors() )
		{
			$this->_identity = new PlatformUserIdentity( $this->username, $this->password );

			if ( $this->_identity->authenticate() )
			{
				return true;
			}

			$this->addError( null, 'Invalid user name and password combination.' );
		}

		return false;
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 *
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		$_identity = $this->_identity;

		if ( empty( $_identity ) )
		{
			$_identity = new PlatformUserIdentity( $this->username, $this->password );

			if ( !$_identity->authenticate() )
			{
				$_identity = null;

				return false;
			}
		}

		if ( \CBaseUserIdentity::ERROR_NONE == $_identity->errorCode )
		{
			$this->_identity = $_identity;
			$_duration = $this->rememberMe ? 3600 * 24 * 30 : 0;

			return Pii::user()->login( $_identity, $_duration );
		}

		return false;
	}

	/**
	 * @param PlatformUserIdentity $identity
	 *
	 * @return LoginForm
	 */
	public function setIdentity( $identity )
	{
		$this->_identity = $identity;

		return $this;
	}

	/**
	 * @return PlatformUserIdentity
	 */
	public function getIdentity()
	{
		return $this->_identity;
	}
}
