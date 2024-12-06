# Security

## Inscription

**Endpoint** : `/api/security/registration`

**Body**
```json
{
  "email": "test1z2@test.com",
  "password": "password",
  "name": "souvignet",  // (facultatif)
  "firstname": "baudry" // (facultatif)
}
```
## Connexion

**Endpoint** : `/api/login-check`

**Body**
```json
{
  "email": "test@test.com",
  "password": "password"
}
```



