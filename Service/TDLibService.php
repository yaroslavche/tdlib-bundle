<?php
declare(strict_types=1);

namespace Yaroslavche\TDLibBundle\Service;

use TDLib\JsonClient;

Error_Reporting(E_ALL);
ini_set('display_errors', '1');

class TDLibService
{
    public const AUTHORIZATION_STATE_READY = 'authorizationStateReady';
    public const AUTHORIZATION_STATE_WAIT_PHONE_NUMBER = 'authorizationStateWaitPhoneNumber';
    public const AUTHORIZATION_STATE_WAIT_CODE = 'authorizationStateWaitCode';

    /**
     * @var JsonClient $clients
     */
    private $client;

    /**
     * @var string $authorizationState
     */
    private $authorizationState;

    public function __construct(array $parameters, array $client)
    {
        $this->client = new JsonClient();
        $this->setTdlibParameters($parameters);
        $this->setDatabaseEncryptionKey($client['encryption_key']);
    }

    private function checkReceivedResponses()
    {
        $receivedResponses = $this->client->getReceivedResponses();
        dump($receivedResponses);
        foreach ($receivedResponses as $response){
            $response = json_decode($response);
            $type = $response->{'@type'} ?? '';
            switch ($type){
                case 'updateAuthorizationState':
                    $this->authorizationState = $response->{'authorization_state'}->{'@type'};
                    break;
            }
        }
    }

    private function query(array $queryArray, ?int $timeout = null): ?\stdClass
    {
        $responseObject = null;
        try {
            $responseJsonString = $this->client->query(json_encode($queryArray), $timeout ?? 1);
            $responseObject = json_decode($responseJsonString);
            $this->checkReceivedResponses();
        } catch (\Exception $exception) {
            dump(__CLASS__ . '::' . __METHOD__, $exception);
        }
        return $responseObject;
    }

    /**
     * authorizationState sets in $this->checkReceivedResponses() if responses contains type updateAuthorizationState
     *
     * @return string $authorizationState
     */
    public function getAuthorizationState(): string
    {
        $queryArray = ['@type' => 'getAuthorizationState'];
        $this->query($queryArray);
        return $this->authorizationState;
    }

    public function setTdlibParameters(array $parameters = [])
    {
        $queryArray = [
            '@type' => 'setTdlibParameters',
            'parameters' => $parameters
        ];
        return $this->query($queryArray);
    }

    public function setDatabaseEncryptionKey(?string $newEncryptionKey = null)
    {
        $queryArray = [
            '@type' => 'setDatabaseEncryptionKey'
        ];
        if (!empty($newEncryptionKey)) {
            $queryArray['new_encryption_key'] = $newEncryptionKey;
        }
        return $this->query($queryArray);
    }

    public function setAuthenticationPhoneNumber(string $phoneNumber, bool $allowFlashCall = false, bool $isCurrentPhoneNumber = false)
    {
        $queryArray = [
            '@type' => 'setAuthenticationPhoneNumber',
            'phone_number' => $phoneNumber,
            'allow_flash_call' => $allowFlashCall,
            'is_current_phone_number' => $isCurrentPhoneNumber
        ];
        return $this->query($queryArray, 3);
    }

    public function checkAuthenticationCode(string $code, string $firstName, string $lastName)
    {
        $queryArray = [
            '@type' => 'checkAuthenticationCode',
            'code' => $code,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
        return $this->query($queryArray);
    }

    public function searchPublicChat(string $username)
    {
        $queryArray = [
            '@type' => 'searchPublicChat',
            'username' => $username
        ];
        return $this->query($queryArray);
    }

    public function logOut()
    {
        $queryArray = [
            '@type' => 'logOut'
        ];
        return $this->query($queryArray);
    }

    public function getMe()
    {
        $queryArray = [
            '@type' => 'getMe'
        ];
        return $this->query($queryArray);
    }
}

// https://core.telegram.org/tdlib/docs/classtd_1_1td__api_1_1_function.html
/*
- [ ] acceptCall
- [ ] acceptTermsOfService
- [ ] addChatMember
- [ ] addChatMembers
- [ ] addFavoriteSticker
- [ ] addLocalMessage
- [ ] addNetworkStatistics
- [ ] addProxy
- [ ] addRecentlyFoundChat
- [ ] addRecentSticker
- [ ] addSavedAnimation
- [ ] addStickerToSet
- [ ] answerCallbackQuery
- [ ] answerCustomQuery
- [ ] answerInlineQuery
- [ ] answerPreCheckoutQuery
- [ ] answerShippingQuery
- [ ] blockUser
- [ ] cancelDownloadFile
- [ ] cancelUploadFile
- [ ] changeChatReportSpamState
- [ ] changeImportedContacts
- [ ] changePhoneNumber
- [ ] changeStickerSet
- [ ] checkAuthenticationBotToken
- [X] checkAuthenticationCode
- [ ] checkAuthenticationPassword
- [ ] checkChangePhoneNumberCode
- [ ] checkChatInviteLink
- [ ] checkChatUsername
- [ ] checkDatabaseEncryptionKey
- [ ] checkEmailAddressVerificationCode
- [ ] checkPhoneNumberConfirmationCode
- [ ] checkPhoneNumberVerificationCode
- [ ] cleanFileName
- [ ] clearAllDraftMessages
- [ ] clearImportedContacts
- [ ] clearRecentlyFoundChats
- [ ] clearRecentStickers
- [ ] close
- [ ] closeChat
- [ ] closeSecretChat
- [ ] createBasicGroupChat
- [ ] createCall
- [ ] createNewBasicGroupChat
- [ ] createNewSecretChat
- [ ] createNewStickerSet
- [ ] createNewSupergroupChat
- [ ] createPrivateChat
- [ ] createSecretChat
- [ ] createSupergroupChat
- [ ] createTemporaryPassword
- [ ] deleteAccount
- [ ] deleteChatHistory
- [ ] deleteChatMessagesFromUser
- [ ] deleteChatReplyMarkup
- [ ] deleteFile
- [ ] deleteLanguagePack
- [ ] deleteMessages
- [ ] deletePassportElement
- [ ] deleteProfilePhoto
- [ ] deleteSavedCredentials
- [ ] deleteSavedOrderInfo
- [ ] deleteSupergroup
- [ ] destroy
- [ ] disableProxy
- [ ] discardCall
- [ ] disconnectAllWebsites
- [ ] disconnectWebsite
- [ ] downloadFile
- [ ] editCustomLanguagePackInfo
- [ ] editInlineMessageCaption
- [ ] editInlineMessageLiveLocation
- [ ] editInlineMessageMedia
- [ ] editInlineMessageReplyMarkup
- [ ] editInlineMessageText
- [ ] editMessageCaption
- [ ] editMessageLiveLocation
- [ ] editMessageMedia
- [ ] editMessageReplyMarkup
- [ ] editMessageText
- [ ] editProxy
- [ ] enableProxy
- [ ] finishFileGeneration
- [ ] forwardMessages
- [ ] generateChatInviteLink
- [ ] getAccountTtl
- [ ] getActiveLiveLocationMessages
- [ ] getActiveSessions
- [ ] getAllPassportElements
- [ ] getArchivedStickerSets
- [ ] getAttachedStickerSets
- [X] getAuthorizationState
- [ ] getBasicGroup
- [ ] getBasicGroupFullInfo
- [ ] getBlockedUsers
- [ ] getCallbackQueryAnswer
- [ ] getChat
- [ ] getChatAdministrators
- [ ] getChatEventLog
- [ ] getChatHistory
- [ ] getChatMember
- [ ] getChatMessageByDate
- [ ] getChatMessageCount
- [ ] getChatPinnedMessage
- [ ] getChatReportSpamState
- [ ] getChats
- [ ] getConnectedWebsites
- [ ] getContacts
- [ ] getCountryCode
- [ ] getCreatedPublicChats
- [ ] getDeepLinkInfo
- [ ] getFavoriteStickers
- [ ] getFile
- [ ] getFileExtension
- [ ] getFileMimeType
- [ ] getGameHighScores
- [ ] getGroupsInCommon
- [ ] getImportedContactCount
- [ ] getInlineGameHighScores
- [ ] getInlineQueryResults
- [ ] getInstalledStickerSets
- [ ] getInviteText
- [ ] getLanguagePackString
- [ ] getLanguagePackStrings
- [ ] getLocalizationTargetInfo
- [ ] getMapThumbnailFile
- [X] getMe
- [ ] getMessage
- [ ] getMessages
- [ ] getNetworkStatistics
- [ ] getOption
- [ ] getPassportAuthorizationForm
- [ ] getPassportElement
- [ ] getPasswordState
- [ ] getPaymentForm
- [ ] getPaymentReceipt
- [ ] getPreferredCountryLanguage
- [ ] getProxies
- [ ] getProxyLink
- [ ] getPublicMessageLink
- [ ] getRecentInlineBots
- [ ] getRecentlyVisitedTMeUrls
- [ ] getRecentStickers
- [ ] getRecoveryEmailAddress
- [ ] getRemoteFile
- [ ] getRepliedMessage
- [ ] getSavedAnimations
- [ ] getSavedOrderInfo
- [ ] getScopeNotificationSettings
- [ ] getSecretChat
- [ ] getStickerEmojis
- [ ] getStickers
- [ ] getStickerSet
- [ ] getStorageStatistics
- [ ] getStorageStatisticsFast
- [ ] getSupergroup
- [ ] getSupergroupFullInfo
- [ ] getSupergroupMembers
- [ ] getSupportUser
- [ ] getTemporaryPasswordState
- [ ] getTextEntities
- [ ] getTopChats
- [ ] getTrendingStickerSets
- [ ] getUser
- [ ] getUserFullInfo
- [ ] getUserPrivacySettingRules
- [ ] getUserProfilePhotos
- [ ] getWallpapers
- [ ] getWebPageInstantView
- [ ] getWebPagePreview
- [ ] importContacts
- [ ] joinChat
- [ ] joinChatByInviteLink
- [ ] leaveChat
- [X] logOut
- [ ] openChat
- [ ] openMessageContent
- [ ] optimizeStorage
- [ ] parseTextEntities
- [ ] pingProxy
- [ ] pinSupergroupMessage
- [ ] processDcUpdate
- [ ] readAllChatMentions
- [ ] recoverAuthenticationPassword
- [ ] recoverPassword
- [ ] registerDevice
- [ ] removeContacts
- [ ] removeFavoriteSticker
- [ ] removeProxy
- [ ] removeRecentHashtag
- [ ] removeRecentlyFoundChat
- [ ] removeRecentSticker
- [ ] removeSavedAnimation
- [ ] removeStickerFromSet
- [ ] removeTopChat
- [ ] reorderInstalledStickerSets
- [ ] reportChat
- [ ] reportSupergroupSpam
- [ ] requestAuthenticationPasswordRecovery
- [ ] requestPasswordRecovery
- [ ] resendAuthenticationCode
- [ ] resendChangePhoneNumberCode
- [ ] resendEmailAddressVerificationCode
- [ ] resendPhoneNumberConfirmationCode
- [ ] resendPhoneNumberVerificationCode
- [ ] resetAllNotificationSettings
- [ ] resetNetworkStatistics
- [ ] searchCallMessages
- [ ] searchChatMembers
- [ ] searchChatMessages
- [ ] searchChatRecentLocationMessages
- [ ] searchChats
- [ ] searchChatsOnServer
- [ ] searchContacts
- [ ] searchHashtags
- [ ] searchInstalledStickerSets
- [ ] searchMessages
- [X] searchPublicChat
- [ ] searchPublicChats
- [ ] searchSecretMessages
- [ ] searchStickers
- [ ] searchStickerSet
- [ ] searchStickerSets
- [ ] sendBotStartMessage
- [ ] sendCallDebugInformation
- [ ] sendCallRating
- [ ] sendChatAction
- [ ] sendChatScreenshotTakenNotification
- [ ] sendChatSetTtlMessage
- [ ] sendCustomRequest
- [ ] sendEmailAddressVerificationCode
- [ ] sendInlineQueryResultMessage
- [ ] sendMessage
- [ ] sendMessageAlbum
- [ ] sendPassportAuthorizationForm
- [ ] sendPaymentForm
- [ ] sendPhoneNumberConfirmationCode
- [ ] sendPhoneNumberVerificationCode
- [ ] setAccountTtl
- [ ] setAlarm
- [X] setAuthenticationPhoneNumber
- [ ] setBio
- [ ] setBotUpdatesStatus
- [ ] setChatClientData
- [ ] setChatDraftMessage
- [ ] setChatMemberStatus
- [ ] setChatNotificationSettings
- [ ] setChatPhoto
- [ ] setChatTitle
- [ ] setCustomLanguagePack
- [ ] setCustomLanguagePackString
- [X] setDatabaseEncryptionKey
- [ ] setFileGenerationProgress
- [ ] setGameScore
- [ ] setInlineGameScore
- [ ] setName
- [ ] setNetworkType
- [ ] setOption
- [ ] setPassportElement
- [ ] setPassportElementErrors
- [ ] setPassword
- [ ] setPinnedChats
- [ ] setProfilePhoto
- [ ] setRecoveryEmailAddress
- [ ] setScopeNotificationSettings
- [ ] setStickerPositionInSet
- [ ] setSupergroupDescription
- [ ] setSupergroupStickerSet
- [ ] setSupergroupUsername
- [ ] setTdlibParameters
- [ ] setUsername
- [ ] setUserPrivacySettingRules
- [ ] terminateAllOtherSessions
- [ ] terminateSession
- [ ] testCallBytes
- [ ] testCallEmpty
- [ ] testCallString
- [ ] testCallVectorInt
- [ ] testCallVectorIntObject
- [ ] testCallVectorString
- [ ] testCallVectorStringObject
- [ ] testGetDifference
- [ ] testNetwork
- [ ] testSquareInt
- [ ] testUseError
- [ ] testUseUpdate
- [ ] toggleBasicGroupAdministrators
- [ ] toggleChatDefaultDisableNotification
- [ ] toggleChatIsMarkedAsUnread
- [ ] toggleChatIsPinned
- [ ] toggleSupergroupInvites
- [ ] toggleSupergroupIsAllHistoryAvailable
- [ ] toggleSupergroupSignMessages
- [ ] unblockUser
- [ ] unpinSupergroupMessage
- [ ] upgradeBasicGroupChatToSupergroupChat
- [ ] uploadFile
- [ ] uploadStickerFile
- [ ] validateOrderInfo
- [ ] viewMessages
- [ ] viewTrendingStickerSets
*/
