import subprocess
import os
import sys

# execution/test_orchestrator.py

# Simular importación de logger (para seguir AGENTS.md)
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from utils import logger

def run_php_tests():
    php_path = r"C:\xampp\php\php.exe"
    test_runner = r"tests\run_master.php"
    
    print(f"--- Iniciando Orquestador de Tests (Software Factory) ---")
    logger.log("Iniciando ejecución de suite de tests.")
    
    try:
        result = subprocess.run([php_path, test_runner], capture_output=True, text=True, check=True)
        print(result.stdout)
        logger.log("Suite de tests completada exitosamente.")
        return True
    except subprocess.CalledProcessError as e:
        print(f"Error en la ejecución de tests:\n{e.output}")
        logger.log(f"FALLO en la suite de tests: {e.output}")
        return False

if __name__ == "__main__":
    success = run_php_tests()
    if not success:
        sys.exit(1)
