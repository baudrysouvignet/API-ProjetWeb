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

## Recuperer les info du compte

**Endpoint** : GET `/api/user/info`

**Body**
```json
noBody
```

## Recuperer les platforms

**Endpoint** : GET `/api/user/platforms`

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

## Récuperer les comptes du user

**Endpoint** : GET `/api/user/platforms`

**Body**
```json
noBody
```

## Créer un projet

**Endpoint** : POST `api/user/projects/create`

**Body**
```json
{
  "properties": {
    "type": {"type": "string"},
    "title": {"type": "string"},
    "info": {
      "type": "object",
      "properties": {
        "account": {"type": "integer"},
        "id": {"type": "integer"},
        "issues": {"type": "integer"}
      },
      "required": ["account", "id", "issues"]
    }
  },
  "required": ["type", "title", "info"]
}
```

## Recuperer les projets

**Endpoint** : GET `/api/user/projects`

**Body**
```json
noBody
```