import pyAesCrypt
import zipfile
import ast
import os

from os import stat, remove
from Crypto.Cipher import PKCS1_OAEP
from Crypto.PublicKey import RSA

# This function takes a file and generates a zip with name as filename.zip
def compressFile(filename):
    head, tail = os.path.split(filename)
    with zipfile.ZipFile(filename + '.zip', mode='w') as zipOut:
        zipOut.write(filename, arcname=tail, compress_type=zipfile.ZIP_DEFLATED)

# Function to decompress a zip file
def uncompressFile(filename):
    head, tail = os.path.split(filename)
    with zipfile.ZipFile(filename, mode='r') as zipIn:
        zipIn.extractall(path=head)

# This function encrypts the data using a public key. Please note that the data has to be smaller
# than the size of the public key that you are using.
def encryptUsingPublicKey(data, publicKeyPath):
    with open(publicKeyPath, 'rb') as publicKeyFile:
        publicKey = RSA.importKey(publicKeyFile.read())
        cipher = PKCS1_OAEP.new(publicKey)
        cipherText = cipher.encrypt(data.encode('UTF-8'))
        return cipherText.hex()

# Decrypt the data using the private key. The cipherText passed should have been generated using the
# corresponding public key.
def decryptDataUsingPrivateKey(cipherText, privateKeyPath, privateKeyPassphrase):
    with open(privateKeyPath, 'rb') as privateKeyFile:
        privateKey = RSA.importKey(privateKeyFile.read(), passphrase=privateKeyPassphrase)
        cipher = PKCS1_OAEP.new(privateKey)
        clearText = cipher.decrypt(bytes.fromhex(cipherText)).decode('UTF-8')
        return clearText


def encryptFileWithAESKey(fIn, fOut, password):
    bufferSize = 64 * 1024
    pyAesCrypt.encryptStream(fIn, fOut, password, bufferSize)


def decryptFileWithAESKey(filename, fIn, fOut, password):
    try:
        bufferSize = 64 * 1024
        encFileSize = stat(filename).st_size
        pyAesCrypt.decryptStream(
            fIn, fOut, password, bufferSize, encFileSize)
    except ValueError:
    # remove output file on error
        remove("m1.txt")
