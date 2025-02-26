from passlib.context import CryptContext
from jose import JWTError, jwt
from datetime import datetime, timedelta
from typing import Union

# Paramètres pour le hachage et la vérification des mots de passe
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

SECRET_KEY = "A3érezkfz4964svf$^ù!:grzg79zr8g4s3"  # À remplacer par une clé secrète forte
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 30  # Expiration du token

# Fonction pour hacher les mots de passe
def hash_password(password: str) -> str:
    return pwd_context.hash(password)

# Fonction pour vérifier les mots de passe
def verify_password(plain_password: str, hashed_password: str) -> bool:
    return pwd_context.verify(plain_password, hashed_password)

# Fonction pour créer un token JWT
def create_access_token(data: dict, expires_delta: Union[timedelta, None] = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)  # Default to 15 minutes
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

# Fonction pour vérifier un token JWT
def verify_token(token: str) -> Union[dict, None]:
    try:
        # Décoder le token et valider sa signature et son expiration
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        # Optionnellement, tu peux valider ici d'autres informations dans le payload
        return payload  # Retourne les informations utilisateur du token

    except JWTError as e:
        # Si une erreur se produit, tu peux renvoyer plus de détails pour le débogage
        print(f"JWTError: {e}")
        return None  # Si le token est invalide ou expiré

    except Exception as e:
        # Gérer toute autre exception potentielle
        print(f"Exception: {e}")
        return None
