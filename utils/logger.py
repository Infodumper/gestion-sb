# utils/logger.py

import logging

logging.basicConfig(
    filename="logs/system.log",
    level=logging.INFO
)

def log(msg):
    logging.info(msg)