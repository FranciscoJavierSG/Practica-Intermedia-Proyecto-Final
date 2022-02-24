#!/usr/bin/env python 1
# -*- coding: utf-8 -*-

from pydoc import cli
import paho.mqtt.client as mqtt
import sys

# The callback for when the client receives a CONNACK response from the server.
def on_connect(client, userdata, flags, rc):
    print("Conectado al broker MQTT. - Código de resultado: " + str(rc))
    # Para ver qué significa cada código de resultado, está la documentación de MQTT:
    # https://docs.oasis-open.org/mqtt/mqtt/v5.0/os/mqtt-v5.0-os.html#_Toc3901074
    # Punto 3.2.2.2 

    # Subscribing in on_connect() means that if we lose the connection and reconnect, then subscriptions will be renewed.
    client.subscribe("Datco/Prueba")

# The callback for when a PUBLISH message is received from the server.
def on_message(client, userdata, msg):
    print(msg.topic + ": " + str(msg.payload))

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

try:
    client.connect("190.110.108.59", 1883, 60)
except:
    print("No se pudo conectar con el broker MQTT")
    sys.exit()

client.username_pw_set("admin","Datco_admin") # Tener ojo con esto, puede estar mal

try:
    client.loop_forever()
except KeyboardInterrupt: # Presionar Ctrl + C para salir
    print("Cerrando...")