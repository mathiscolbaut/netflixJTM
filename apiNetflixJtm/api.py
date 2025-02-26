from fastapi import FastAPI, Depends, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware  # Importer le middleware CORS
from sqlalchemy.orm import Session
from models import User
from database import SessionLocal
from schemas import UserCreate, UserLogin, UserInDB
from utils import hash_password, verify_password, create_access_token, verify_token
from fastapi.security import OAuth2PasswordBearer

# Création de l'application FastAPI
app = FastAPI()

# Ajouter le middleware CORS
origins = [
    "http://localhost",  # Autoriser les requêtes depuis localhost
    "http://127.0.0.1",  # Autoriser les requêtes depuis localhost
    "http://localhost:8080",  # Si vous avez un front-end local
    "http://127.0.0.1:8080",  # Si vous avez un front-end local
    "*",  # Ou utiliser "*" pour autoriser toutes les origines (pas recommandé en production)
]

# Ajouter le middleware CORS pour permettre les requêtes depuis ces origines
app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,  # Liste des origines autorisées
    allow_credentials=True,
    allow_methods=["*"],  # Autoriser toutes les méthodes (GET, POST, PUT, DELETE, etc.)
    allow_headers=["*"],  # Autoriser tous les headers
)

# Dépendance pour obtenir la session de la base de données
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

@app.post("/signup", response_model=UserInDB)
def signup(user: UserCreate, db: Session = Depends(get_db)):
    # Vérifier si le nom d'utilisateur est déjà pris
    db_user = db.query(User).filter(User.username == user.username).first()
    if db_user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Nom d'utilisateur déjà pris"
        )

    # Vérifier si l'email est déjà pris
    db_user = db.query(User).filter(User.email == user.email).first()
    if db_user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Email déjà enregistré"
        )

    # Hash le mot de passe et crée le nouvel utilisateur
    hashed_password = hash_password(user.password)
    new_user = User(username=user.username, email=user.email, hashed_password=hashed_password)
    db.add(new_user)
    db.commit()
    db.refresh(new_user)

    # Retourner les informations de l'utilisateur créé
    return new_user

# Route de connexion
@app.post("/login")
def login(user: UserLogin, db: Session = Depends(get_db)):
    db_user = db.query(User).filter(User.username == user.username).first()
    if not db_user or not verify_password(user.password, db_user.hashed_password):
        raise HTTPException(status_code=400, detail="Invalid username or password")
    
    access_token = create_access_token(data={"sub": db_user.username})
    return {"access_token": access_token, "token_type": "bearer"}

# Route de déconnexion
@app.post("/logout")
def logout():
    return {"msg": "You are now logged out!"}


oauth2_scheme = OAuth2PasswordBearer(tokenUrl="login")

@app.get("/user")
def get_user(token: str = Depends(oauth2_scheme)):
    user_data = verify_token(token)
    if user_data:
        return {"user_data": user_data}
    else:
        raise HTTPException(status_code=401, detail="Token invalid or expired")