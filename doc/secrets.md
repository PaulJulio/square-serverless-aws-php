# Storing your Square Secrets in AWS Secrets Manager

To get your information:
* Sign in to the [developer dashboard][1]
* Create a new app named something like serverless-aws
* View the credentials page for the app, find the Application ID and Personal Access Token
* View the OAuth page for the app, find the Application Secret
* In a new tab, open the [AWS Console][2] and log in
* Get to the [AWS Secrets Manager][3] product
* Store a new secret -> Other type of secrets
* first key/value is `client-id` and your client ID from the Square app
* second key/value is `client-secret` and your Personal Access Token from the Square app
* third key/value is `oauth-secret` and your Application Secret from the Squrae app
* check your Plaintext, it should look like
```$json
{
  "client-id": "sq0idp-KVnxZwJ4pnHpxmKT5QxfHg",
  "client-secret": "EAAAXXXX482sXXXXXXXXh6Qke8aP6eSWpkSjnXXXXXXXXXKGq1uX7i1KXXXXXXX_",
  "oauth-secret": "sq0csp-a8aXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX5P4"
}
```
* click next, give you secret a name and a description
* choose disable automatic rotation for now, don't risk it for a hack-week demo
* on the last screen store your secret
* once the secret it stored, click on it to bring up the detail, find your `Secret ARN`
* update the `serverless.yml` with your ARN (todo: how can this step be automated?)

[1]: https://connect.squareup.com/apps
[2]: https://us-west-1.console.aws.amazon.com/console/home
[3]: https://us-west-1.console.aws.amazon.com/secretsmanager/home?region=us-west-1#/listSecrets