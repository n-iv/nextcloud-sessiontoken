<?php
/**
 * Nextcloud - sessiontoken
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Benjamin Sonntag <benjamin@octopuce.fr>
 * @copyright Benjamin Sonntag 2021
 */

namespace OCA\Sessiontoken\Controller;

use OC\Group\Manager;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\IURLGenerator;
use OCP\ILogger;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\ISession;
use OCP\IUser;
use OCP\IUserManager;
use OCP\Security\ISecureRandom;
use OCP\Activity\IManager;
use OCA\Settings\Activity\Provider;
use OC\Authentication\Token\IProvider;
use OC\Authentication\Token\IToken;
use OCP\IUserSession;
use Redis;

class SessiontokenController extends Controller {
	/** @var IUserManager */
	private $userManager;
	/** @var IGroupManager|Manager */
	private $groupManager;
	/** @var IUserSession */
	private $userSession;
	/** @var ISession */
	private $session;
	/** @var IConfig */
	private $config;
	/** @var ILogger */
	private $logger;
	/** @var IL10N */
	private $l;
	/** @var Manager|PublicEmitter $manager */
	private $manager;
    /** @var IProvider */
    private $tokenProvider;
    /** @var ISecureRandom */
    private $random;
    /** @var IManager */
    private $activityManager;

    private $uid;

	/**
	 * @param string $appName
	 * @param Manager $manager
	 * @param IRequest $request
	 * @param IUserManager $userManager
	 * @param IGroupManager $groupManager
	 * @param IUserSession $userSession
	 * @param ISession $session
	 * @param IConfig $config
	 * @param ILogger $logger
     * @param ISecureRandom $random
     * @param IProvider $tokenProvider
     * @param IManager $activityManager
	 * @param IURLGenerator $urlGenerator
	 */
	public function __construct($appName,
                                Manager $manager,
								IRequest $request,
								IUserManager $userManager,
								IGroupManager $groupManager,
								IUserSession $userSession,
								ISession $session,
								IConfig $config,
								ILogger $logger,
                                ISecureRandom $random,
                                IProvider $tokenProvider,
                                IManager $activityManager,
                                IURLGenerator $urlGenerator
    ) {
		parent::__construct($appName, $request);
		$this->manager = $manager;
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->userSession = $userSession;
		$this->session = $session;
		$this->config = $config;
		$this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
        $this->tokenProvider = $tokenProvider;
        $this->activityManager = $activityManager;
        $this->random = $random;
	}


	/**
	 * @UseSession
	 * @NoCSRFRequired
     * @OnlyUnauthenticatedUsers
     * @PublicPage
	 *
	 * @return Http\RedirectResponse
     * 
     * returns a token that can be used to impersonate as a user.
     * you can then use that in your web applications to browse for files or upload/download in the name of that user.
     * requires apikey=<a key> in the POSTed data, which should match the hashed password stored in config.php
     * requires in the POSTed data the user and the application name (that will be stored in the token table)
	 */
	public function token() {

        // key check
        $apikey_hash = $this->config->getSystemValue('sessiontoken_apikey_hash');
        if (!$apikey_hash) {
            header("HTTP/1.0 403 apikey not configured");
            exit();
        }
        if (!isset($_POST["apikey"]) || !password_verify($_POST["apikey"], $apikey_hash) ) {
            header("HTTP/1.0 403 apikey invalid");
            exit();
        }
        
        $userId = $_POST["user"];
        $name = trim($_POST["name"]);

		$user = $this->userManager->get($userId);
		if ($user === null) {
			return new JSONResponse( [ 'message' => 'User not found', ], Http::STATUS_NOT_FOUND	);
		}

		$this->logger->warning(
			sprintf('Getting a token for user %u',$userId),	['app' => 'sessiontoken']
		);
        
        $this->uid=$user->getUID();

        $details=["password"=>"empty", "loginName" => $this->uid ];
        
        $this->manager->emit('\OC\User', 'preLogin', $details );
        if (! $this->userSession->completeLogin($user,$details,true)) {
                $this->logger->warning( "completeLogin failed", [ 'app' => 'sessiontoken'] );
                return new JSONResponse( [ 'message' => 'Login Failed', ], Http::STATUS_NOT_FOUND );
        }
  
        if (mb_strlen($name) > 128) {
            $name = mb_substr($name, 0, 120) . '…';
        }

        $token = $this->generateRandomDeviceToken();
        $deviceToken = $this->tokenProvider->generateToken($token, $this->uid, $this->uid, null, $name, IToken::PERMANENT_TOKEN);
        $tokenData = $deviceToken->jsonSerialize();

        $this->publishActivity(Provider::APP_TOKEN_CREATED, $deviceToken->getId(), ['name' => $deviceToken->getName()]);

        return new JSONResponse([
            'token' => $token,
            'loginName' => $this->uid,
            'deviceToken' => $tokenData,
        ]);

	} 


    private function generateRandomDeviceToken() {
        $groups = [];
        for ($i = 0; $i < 5; $i++) {
            $groups[] = $this->random->generate(5, ISecureRandom::CHAR_HUMAN_READABLE);
        }
        return implode('-', $groups);
    }


    /**                                                                                                                                                      
     * @param string $subject
     * @param int $id
     * @param array $parameters
     */
    private function publishActivity(string $subject, int $id, array $parameters = []): void {
        $event = $this->activityManager->generateEvent();
        $event->setApp('sessiontoken')
            ->setType('security')
            ->setAffectedUser($this->uid)
            ->setAuthor($this->uid)
            ->setSubject($subject, $parameters)
            ->setObject('app_token', $id, 'App Password');

        try {
            $this->activityManager->publish($event);
        } catch (BadMethodCallException $e) {
            $this->logger->warning('could not publish activity', ['exception' => $e]);
        }
    }


} // SettingsController

