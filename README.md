# SessionToken

This application provides 1 URL endpoint to get a permanent token to be able to impersonate as any user of the nextcloud from other services (like bbb cloud from Education Nationale or Nextcloud-Copy from Octopuce.)

Heavily based on impersonate app, but can be used even if the user never logged-in.

# Installation

- first define a 32 characters (minimum) password and write it down in your personal password vault 

- then clone the repository in your apps/ folder and name it "sessiontoken" :

```
# from your nextcloud root folder :
cd apps
git clone https://gitlab.octopuce.fr/octopuce-public/nextcloud-sessiontoken.git sessiontoken
cd sessiontoken
```

- now hash the password using:

```
php hash-apikey.php <your password>
```

- store the hashed password into config/config.php in a key named "sessiontoken_apikey_hash"
- in the nextcloud application manager, enable the application named "sessiontoken" 

now you can ask for a token for any user on this Nextcloud by calling the following endpoint : 

```
curl -XPOST https://yournextcloud/apps/sessiontoken/token -d "apikey=yourpassword&user=theusername&name=your-application"
```


The answer will be formatted as following

```
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

your-application is a mandatory freeform string, that will be visible in the user profile in its "security" tab.

you can now use the login "theusername" and the password wbx8n-8KAn5-cfDZa-ABxDj-TGHJX to connect to the webdav or NC OCS API as this user.



