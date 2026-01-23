# API

Brightspace "SDK".

## Configuration

The configuration can be set as follows. The sections are documented below the example.
```php
$config = [
	'section1' => [
		'name1' => 'value1',
		'name2' => 'value2'
	],
	'section2' => [ ... ]
]
```

### `oauth2`
This structure follows League oauth2 documentation on https://oauth2-client.thephpleague.com/usage/
and Brightspace API documentation at https://docs.valence.desire2learn.com/basic/oauth2.html

| parameter               | value                                                                                                |
|-------------------------|------------------------------------------------------------------------------------------------------|
| `clientId`              | Client id                                                                                            |
| `clientSecret`          | Client secret                                                                                        |
| `urlAuthorize`          | Oauth2 authorization url                                                                             |
| `urlAccessToken`        | Oauth2 access token endpoint url                                                                     |
| `scopes`                | Space-separated list of scopes to request ( `<resource-group>:<resource>:<action>`)                  |
| `serviceTokenHandler`   | _Optional._ Indicate how to store a service token [`file` \| `db` ]                                  |
| `serviceTokenTableName` | _Optional._ Table name in case `db` token handler is used, defaults to `service_token`               |
| `serviceTokenFile`      | _Required if `file` handler is selected._ Filename to store the service token                        |
| `serviceAuthType`       | _Optional._ Authentication method for service accounts. [ default: `userAccount` \| `serviceAccount` |

The following parameters are used if the `serviceAccount` auth method is selected:

| parameter         | value                                                   |
|-------------------|---------------------------------------------------------|
| `serviceClientId` | Client id for the service account.                      |
| `jwkPrivate`      | `string` Private key                                    |
| `jwkPublic`       | `string` Corresponding public key in PEM format         |
| `keyId`           | _Optional._ The id of the key to use (defaults to `bsm` |


### `app`
This section is used for app-specific settings and can be extended with app-specific settings.

| parameter        | value                                                                                                                                                                |
|------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `sessionHandler` | _Optional._ Set to `db` to store sessions in a database, omit otherwise.                                                                                             |
| `sessionPath`    | _Optional._ Path to file system where sessions are stored (overriding PHP default), or table name in case `db` session handler was selected (defaults to `sessions`) |

### `brightspace`
Settings to indicate where the API can be found.

| parameter        | value                                                                                                                               |
|------------------|-------------------------------------------------------------------------------------------------------------------------------------|
| `url`            | Brightspace instance URL, used to prefix relative paths, e.g. `https://institution.brightspace.com`                                 |
| `api`            | Brightspace API base url, e.g. `https://institution.brightspace.com/d2l/api`                                                        |
| `serviceAccount` | In case the `regularAccount` auth type for service accounts is used, indicate user name of the account that acts as service account |

### `db`
Configure database connection

| parameter  | value                                                                               |
|------------|-------------------------------------------------------------------------------------|
| `dsn`      | The DSN for the database connection (uses `phpsald`, so multihost DSNs are allowed) |
| `username` | Database username                                                                   |
| `password` | Database password                                                                   |
| `options`  | _Optional._ Array of `PDO` options to use (error or fetch modes cannot be changed)  |
| `schema`   | _Optional._ PostgreSQL schema name, if other than the user's default schema         |
| `logLevel` | `Psr\Log\LogLevel` _Optional._ Log level for database issues or debugging           |

### `config`
Settings related to configuration management, optionally using a HVault.

| parameter    | value                                                                         |
|--------------|-------------------------------------------------------------------------------|
| `cachePath`  | Filename to cache the resolved configuration                                  |
| `vaultUri`   | _Required when using vault._ Url of the vault instance                        | 
| `vaultToken` | _Required when using vault._ Token to authenticate with vault                 |
| `vaultPath`  | _Required when using vault._ Path to the secrets for the current app instance |

Vault secrets can be loaded by calling `Vault::secret('secretName')`.

### `smarty`
Settings for the Smarty template engine.
__TODO__

## Authentication

### User accounts and 'regular user' service accounts
(default oath2 flow)

### Brightspace service account
Actual service accounts were introduced late 2026. Simply request an access token from the
token url (https://auth.brightspace.com/core/connect/token), but send the `client_credentials`
grant, with options:
* `scope`: _the list of requested scopes_
* `client_assertion_type`: urn:ietf:params:oauth:client-assertion-type:jwt-bearer
* `client_assertion`: JWT string from encoding the `sub`, `iss`, `aud`, `jti` and `exp` claims with the private key

Notes
* `kid` is required, having a single key without id leads to `invalid_grant` errors.