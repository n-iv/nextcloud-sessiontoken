# SessionToken

This application provides 1 URL endpoint to get a permanent token to be able to impersonate as any user of the nextcloud from other services.

Heavily based on impersonate app, but can be used even if the user never logged-in.

## Installation

- Clone the repository in your apps/ folder and name it "sessiontoken":

```bash
# from your nextcloud root folder:
cd apps
git clone https://github.com/n-iv/nextcloud-sessiontoken.git
cd sessiontoken
```

- Now get a random API key and the hashed version of it by using:

```bash
php hash-apikey.php
```

- Keep your cleartext api key in a safe place to be used in your app using sessiontoken.
- Store the hashed api key into config/config.php in a key named "sessiontoken_apikey_hash"
- In the nextcloud application manager, enable the application named "sessiontoken"

That's it, the sessiontoken is now configured.

## Usage

Now you can ask for a token for any user on this Nextcloud by calling the following endpoint:

```bash
curl -XPOST https://yournextcloud/apps/sessiontoken/token -d "apikey=yourapikey&user=theusername&name=your-application"
```

The answer will be formatted as following:

```json
{
    "token":"wbx8n-8KAn5-cfDZa-ABxDj-TGHJX",
    "loginName":"theusername",
    "deviceToken":
        {
            "id":62,
            "name":"your-application",
            "lastActivity":1667504416,
            "type":1,
            "scope":
                {
                    "filesystem":true
                }
        }
}
```

`your-application` is a mandatory freeform string, that will be visible in the user profile in its "security" tab.

You can now use the login "theusername" and the password wbx8n-8KAn5-cfDZa-ABxDj-TGHJX to connect to the webdav or NC OCS API as this user.
