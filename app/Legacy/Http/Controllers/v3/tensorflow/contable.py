import tensorflow as tf
import numpy as np
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense, Embedding, Flatten

# Datos de ejemplo: cédulas y RUC
cedulas = ['0902337278', '0917282220', '0951834092', '1206569004']
ruc = ['1234567890123', '09876543210987', '23456789012345']

# Preprocesamiento de datos
X = cedulas + ruc
y = [0] * len(cedulas) + [1] * len(ruc)  # 0 para cédula, 1 para RUC

# Obtener la longitud máxima de los números
max_length = max(len(num) for num in X)

# Rellenar los números para que tengan la misma longitud
X_padded = [num.ljust(max_length, '0') for num in X]

# Convertir los caracteres a números
char_set = list('0123456789')
char_to_index = {char: index for index, char in enumerate(char_set)}

X_numeric = np.array([[char_to_index[char] for char in num] for num in X_padded])

# Agregar información de provincia y código
provincias = {
    '01': 'Azuay',
    '02': 'Bolivar',
    '03': 'Cañar',
    '04': 'Carchi',
    '05': 'Cotopaxi',
    '06':	'Chimborazo',
    '07':	'El Oro',
    '08':	'Esmeraldas',
    '09':	'Guayas',
    '10':	'Imbabura',
    '11':	'Loja',
    '12':	'Los Ríos',
    '13':	'Manabí',
    '14':	'Morona Santiago',
    '15':	'Napo',
    '16':	'Pastaza',
    '17':	'Pichincha',
    '18':	'Tungurahua',
    '19':	'Zamora Chinchipe',
    '20':	'Galápagos',
    '21':	'Sucumbíos',
    '22':	'Orellana',
    '23':	'Santo Domingo de los Tsáchilas',
    '24':	'Santa Elena',
    '30':	'Número reservado para ecuatorianos registrados en el exterior',
   

}

# Crear el modelo
model = Sequential()
model.add(Embedding(len(char_set), 8, input_length=max_length))
model.add(Flatten())
model.add(Dense(16, activation='relu'))
model.add(Dense(8, activation='relu'))
model.add(Dense(1, activation='sigmoid'))

# Compilar el modelo
model.compile(loss='binary_crossentropy', optimizer='adam', metrics=['accuracy'])

# Convertir las etiquetas a un arreglo NumPy
y = np.array(y)

# Entrenar el modelo
model.fit(X_numeric, y, epochs=50, batch_size=2)

# Función para validar la cédula ecuatoriana
def validar_cedula_ecuatoriana(cedula):
    suma = 0
    for i in range(len(cedula) - 1):
        x = int(cedula[i])
        if i % 2 == 0:
            x = x * 2
            if x > 9:
                x = x - 9
        suma = suma + x

    if suma % 10 != 0:
        resultado = 10 - (suma % 10)
        if int(cedula[-1]) == resultado:
            return True  # La cédula es válida
        else:
            return False  # La cédula NO es válida
    else:
        return False  # La cédula NO es válida

# Hacer predicciones
new_numbers = ['2300295207']
predictions = []

for num in new_numbers:
    # Validar si el número contiene solo dígitos y tiene exactamente 10 dígitos
    if num.isdigit() and len(num) == 10:
        # Extraer el código de provincia del número de cédula
        codigo_provincia = num[:2]

        # Rellenar el número para que tenga la misma longitud
        num_padded = num.ljust(max_length, '0')

        # Convertir los caracteres a números
        num_numeric = np.array([char_to_index[char] for char in num_padded])

        # Hacer la predicción
        prediction = model.predict(np.array([num_numeric]))[0, 0]

        # Clasificar el resultado
        if prediction > 0.5:
            provincia = provincias.get(codigo_provincia, 'Desconocida')  # Obtener el nombre de la provincia
            result = f"provincia {provincia}"
            print(result)
        else:
            # El modelo predice que es una cédula
            if validar_cedula_ecuatoriana(num):
                provincia = provincias.get(codigo_provincia, 'Desconocida')  # Obtener el nombre de la provincia
                print(f'{num} es una cédula ecuatoriana válida de la provincia {provincia}')
            else:
                print(f'{num} Numero de cédula no válido')
    else:
        print(f'{num} no es un número de cédula válido')