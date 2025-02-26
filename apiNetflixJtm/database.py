from databases import Database
import sqlalchemy
from sqlalchemy import create_engine, MetaData
from sqlalchemy.orm import sessionmaker
from sqlalchemy.ext.declarative import declarative_base  # Importation de declarative_base

# URL de connexion à la base de données MariaDB
DATABASE_URL = "mysql://root:AnatriX05092024*@192.168.2.40/dev_netflixjtm"

# Création de Base pour SQLAlchemy
Base = declarative_base()  # Définition de Base

# Connexion à la base de données
engine = create_engine(DATABASE_URL, pool_recycle=3600)
metadata = MetaData()

# SessionLocal pour l'accès aux données
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# Créer toutes les tables définies par SQLAlchemy
def create_tables():
    try:
        # Créer les tables
        Base.metadata.create_all(bind=engine)
        print("Tables créées avec succès.")
    except Exception as e:
        print(f"Une erreur est survenue lors de la création des tables : {e}")
