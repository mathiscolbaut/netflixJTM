from sqlalchemy import Column, Integer, String, Boolean
from database import Base  # Assurez-vous que Base est importé depuis le bon fichier

class User(Base):  # L'héritage de Base ici
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, index=True)
    username = Column(String(255), unique=True, index=True)  # Ajout d'une longueur pour le VARCHAR
    email = Column(String(255), unique=True, index=True)  # Ajout d'une longueur pour le VARCHAR
    hashed_password = Column(String(255))  # Ajout d'une longueur pour le VARCHAR
    role = Column(Integer, default=0)  # Valeur par défaut pour 'role'
    is_active = Column(Boolean, default=True)
