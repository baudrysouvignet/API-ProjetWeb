# Security

## Inscription

**Endpoint** : POST `/api/registration`

**Body**
```json
{
  "type": "object",
  "properties": {
    "firstname": {"type": "string"},
    "name": {"type": "string"},
    "password": {"type": "string"},
    "email": {"type": "string", "format": "email"}
  },
  "required": ["password", "email"]
}
```
## Connexion

**Endpoint** : POST `/api/login`

**Body**
```json
{
  "type": "object",
  "properties": {
    "password": {"type": "string"},
    "email": {"type": "string", "format": "email"}
  },
  "required": ["password", "email"]
} 

```

## Connexion

**Endpoint** : GET `/api/user/info`

**Body**
```json
noBody
```


