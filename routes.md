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

# Jira

## Ajouter un compte

**Endpoint** : POST `/api/user/platforms/jira/add`

**Body**
```json
{
    "type": "object",
    "properties": {
        "url": {"type": "string"},
        "emailJira": {"type": "string", "format": "email"},
        "token": {"type": "string"}
    },
    "required": ["url", "emailJira", "token"]
}
```
