# Ejemplo de implementación de una rentadora de autos

## Introducción

A continuación la documentacion para la implementacion de las nuevas integraciones con las diferentes rentadoras. 

***Es importante hacer uso del SDK de AWS para guardar en un bucket s3 los request y response (XMLs o JSONs) de los llamados de cada servicio. Las credenciales se deben definir en un archivo .env. Link del SDK [aquí](https://aws.amazon.com/es/developer/tools/).***

Los servicios a implementar son los siguientes:

- [Servicio Get Matrix](#servicio-get-matrix)
- [Servicio Get Selection](#servicio-get-selection)
- [Servicio Confirmation](#servicio-confirmation)
- [Servicio My Reservation](#servicio-my-reservation)
- [Servicio Cancel](#servicio-cancel)
- [Servicio Get Offices](#servicio-get-offices)

## Servicio Get Matrix

Se encarga de regresar todos los carros disponibles de la rentadora

### URL

- http://localhost/example-rental-company-implementation/web/companies/get-matrix

### Parámetros de envío

Se envía un JSON vía `POST`

```
{
  "rates": [
    {
      "qualifier": "RC",
      "code": "RGZNET",
      "rate_type_id": "1",
      "payment_option": "1",
      "dummy_iata": "",
      "companyIata": "12345678",
      "rateRqmaps": [],
      "rateType": "just_car",
      "discountCodes": []
    },
    {
      "qualifier": "RC",
      "code": "RGZNET",
      "rate_type_id": "3",
      "payment_option": "1",
      "dummy_iata": "",
      "companyIata": "12345678",
      "rateRqmaps": [],
      "rateType": "full_protection",
      "discountCodes": []
    }
  ],
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "getDataModel": {
    "pickUpLocation": "MIAT01",
    "pickUpAddress": "NA",
    "dropOffLocation": "MIAT01",
    "dropOffAddress": "NA",
    "pickUpDate": "2022-07-18",
    "dropOffDate": "2022-07-25",
    "pickUpHour": "1200",
    "dropOffHour": "1200",
    "cdCode": "NA",
    "pcCode": "NA",
    "country": "US",
    "source": "CO",
    "rateType": "best",
    "lat": "NA",
    "lng": "NA",
    "latDropOff": "NA",
    "lngDropOff": "NA",
    "pickUpFranchiseCode": "291",
    "dropOffFranchiseCode": "291"
  },
  "companyName": "Ace",
  "companyCode": "AC",
  "debug": false,
  "environment": "Test"
}
```

#### Explicación Parámetros de envío

**1. Rates**

Arreglo con las tarifas solicitadas, contiene los siguientes campos:

| VARIABLE       | SIGNIFICADO                                                                             |
|----------------|-----------------------------------------------------------------------------------------|
| qualifier      | Código con tipo el tipo de tarifa enviado, puede ser RC, IT, CD                         |
| code           | Código de la tarifa                                                                     |
| rate_type_id   | Código del tipo de tarifa, usar best o ver sección [Tarifas](#tarifas) columna "TARIFA" |
| payment_option | Tipo de pago, 1: Prepago, 2: POD, 3: Ambas                                              |
| dummy_iata     | Dummy Iata                                                                              |
| companyIata    | Company Iata                                                                            |
| rateRqmaps     | Arreglo con Rqmaps, puede venir vacío                                                   |
| rateType       | Nombre del tipo tarifa, ver seccion ver sección [Tarifas](#tarifas) columna "NOMBRE"    |
| discountCodes  | Arreglo con códigos de descuento, puede venir vacío                                     |

**2. Credentials**

Todas las credenciales necesarias para la conección al API de la rentadora como url, id, host, password, callerCode, code, etc.

**3. GetDataModel**

| VARIABLE             | SIGNIFICADO                                                                                                                                |
|----------------------|--------------------------------------------------------------------------------------------------------------------------------------------|
| pickUpLocation       | Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.                                                                |
| pickUpAddress        | Nombre de la oficina de pickup (por defecto NA)                                                                                            |
| pickUpFranchiseCode  | Código de la oficina de pickup dado por la rentadora. Para búsquedas fuera de aeropuerto se pueden enviar varios separados por coma `(,)`  |
| dropOffLocation      | Ciudad de Dropoff - Basado en código `IATA` de oficina/aeropuerto de dropoff.                                                              |
| dropOffAddress       | Nombre de la oficina de dropoff (por defecto NA)                                                                                           |
| dropOffFranchiseCode | Código de la oficina de dropoff dado por la rentadora. Para búsquedas fuera de aeropuerto se pueden enviar varios separados por coma `(,)` |
| pickUpDate           | Fecha de Pickup - Formato yyyy-mm-dd                                                                                                       |
| dropOffDate          | Fecha de Dropoff - Formato yyyy-mm-dd                                                                                                      |
| pickUpHour           | Hora de Pickup - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                      |
| dropOffHour          | Hora de Dropoff - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                     |
| cdCode               | Código de descuento                                                                                                                        |
| pcCode               | Código de promoción                                                                                                                        |
| country              | País de oficina, código alfa 2 ej: United States (US), Colombia (CO)                                                                       |
| source               | País de fuente, código alfa 2 ej: United States (US), Colombia (CO)                                                                        |
| rateType             | Código tipo de Tarifa, usar `best` o obtener de [Tarifas](#tarifas)                                                                        |
| paymentType          | Tipo de pago de los resultados mostrados (ppd: Pagar Ahora, pod: Pago en Destino)                                                          |
| lat*                 | Latitud de Pickup                                                                                                                          |
| lng*                 | Longitud de Pickup                                                                                                                         |
| latDropOff*          | Latitud de Dropoff                                                                                                                         |
| lngDropOff*          | Longitud de Dropoff                                                                                                                        |

*Solo aplica obligatorio en caso de búsqueda fuera de oficinas.

**4. Otros**

| VARIABLE    | SIGNIFICADO                                                                                                       |
|-------------|-------------------------------------------------------------------------------------------------------------------|
| companyName | Nombre de la empresa que alquila el vehículo                                                                      |
| companyCode | Código de la empresa que alquila el vehículo                                                                      |
| debug       | Si se quiere generar los archivos request y response del servicio en la carpeta "files", valores `true` o `false` |
| environment | Entorno en que se esta llamando el servicio, valores posibles `Test` o `Production`                               |

### Respuesta exitosa (status 200)

```
[
    {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": 1,
        "air": "Yes",
        "trans": "Automatic",
        "passengers": 5,
        "bags": 2,
        "sippCode": "CCAR",
        "doors": 4,
        "img": "https://www.acerentacar.com/CarPics/Nissan Versa.png",
        "carModel": "Nissan Versa",
        "km_included": "Unlimited Miles",
        "payment_option": 1,
        "currency": "USD",
        "realBase": 245.21,
        "realTax": 140.25,
        "rateAmount": 385.46,
        "taxNotIncluded": 0,
        "pickUpLocation": "MIAT01",
        "pickUpFranchiseCode": "291"
    },
    {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": 1,
        "air": "Yes",
        "trans": "Automatic",
        "passengers": 4,
        "bags": 2,
        "sippCode": "ECAR",
        "doors": 4,
        "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
        "carModel": "Toyota Yaris",
        "km_included": "Unlimited Miles",
        "payment_option": 1,
        "currency": "USD",
        "realBase": 253.96,
        "realTax": 142.27,
        "rateAmount": 396.23,
        "taxNotIncluded": 0,
        "pickUpLocation": "MIAT01",
        "pickUpFranchiseCode": "291"
    },
    ...
    {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": 3,
        "air": "Yes",
        "trans": "Automatic",
        "passengers": 4,
        "bags": 2,
        "sippCode": "ECAR",
        "doors": 4,
        "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
        "carModel": "Toyota Yaris",
        "km_included": "Unlimited Miles",
        "payment_option": 1,
        "currency": "USD",
        "realBase": 624.4,
        "realTax": 228.1,
        "rateAmount": 852.5,
        "taxNotIncluded": 0,
        "pickUpLocation": "MIAT01",
        "pickUpFranchiseCode": "291"
    },
    {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": 3,
        "air": "Yes",
        "trans": "Automatic",
        "passengers": 5,
        "bags": 2,
        "sippCode": "CCAR",
        "doors": 4,
        "img": "https://www.acerentacar.com/CarPics/Nissan Versa.png",
        "carModel": "Nissan Versa",
        "km_included": "Unlimited Miles",
        "payment_option": 1,
        "currency": "USD",
        "realBase": 630,
        "realTax": 229.39,
        "rateAmount": 859.39,
        "taxNotIncluded": 0,
        "pickUpLocation": "MIAT01",
        "pickUpFranchiseCode": "291"
    },
]
```

| VARIABLE            | DESCRIPCIÓN                                                                                                                               |
|---------------------|-------------------------------------------------------------------------------------------------------------------------------------------|
| rateType            | Código del tipo de tarifa, usar best o ver sección [Tarifas](#tarifas) columna "TARIFA"                                                   |
| air                 | Si tiene o no aire acondicionado, valores posibles `Yes` y `No`                                                                           |
| trans               | Tipo de transmisión del carro, valores posibles `Automatic` y `Manual`                                                                    |
| passengers          | Número de pasajeros                                                                                                                       |
| bags                | Número de maletas                                                                                                                         |
| sippCode            | Tipo de automóvil según código SIPP                                                                                                       |
| doors               | Número de puertas                                                                                                                         |
| img                 | Imagen del carro                                                                                                                          |
| carModel            | Modelo del carro                                                                                                                          |
| km_included         | Kilometraje/millaje de la tarifa                                                                                                          |
| payment_option      | Tipo de pago, 1: Prepago, 2: POD, 3: Ambas                                                                                                |
| currency            | Moneda de la tarifa, ej: USD, COP, EUR...                                                                                                 |
| realBase            | Esta es la base comisionable, el valor sobre el que comisionan                                                                            |
| realTax             | Estos son los impuestos que realmente tiene la tarifa, y no son comisionables                                                             |
| rateAmount          | Total de la tarifa                                                                                                                        |
| taxNotIncluded      | Valor de impuestos no incluidos                                                                                                           |
| pickUpLocation      | Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.                                                               |
| pickUpFranchiseCode | Código de la oficina de pickup dado por la rentadora. Para búsquedas fuera de aeropuerto se pueden enviar varios separados por coma `(,)` |

### Respuesta con error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```
---

## Servicio Get Selection

Envía la información del auto seleccionado, con las tarifas válidas

### URL

- http://localhost/example-rental-company-implementation/web/companies/get-selection

### Parámetros de envío

Se envía un JSON vía `POST`

```
{
  "rates": [
    {
      "qualifier": "RC",
      "code": "RGZNET",
      "rate_type_id": "1",
      "payment_option": "1",
      "dummy_iata": "",
      "companyIata": "12345678",
      "rateRqmaps": [],
      "rateType": "just_car",
      "discountCodes": []
    },
    {
      "qualifier": "RC",
      "code": "RGZNET",
      "rate_type_id": "3",
      "payment_option": "1",
      "dummy_iata": "",
      "companyIata": "12345678",
      "rateRqmaps": [],
      "rateType": "full_protection",
      "discountCodes": []
    }
  ],
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "getDataModel": {
    "pickUpLocation": "MIAT01",
    "pickUpAddress": "Aeropuerto Intl de Miami, MIA, Florida, Estados Unidos",
    "dropOffLocation": "MIAT01",
    "dropOffAddress": "Aeropuerto Intl de Miami, MIA, Florida, Estados Unidos",
    "pickUpDate": "2022-07-18",
    "dropOffDate": "2022-07-25",
    "pickUpHour": "1200",
    "dropOffHour": "1200",
    "cdCode": "NA",
    "pcCode": "NA",
    "country": "US",
    "source": "CO",
    "rateType": "best",
    "lat": "NA",
    "lng": "NA",
    "latDropOff": "NA",
    "lngDropOff": "NA",
    "sippCode": "ECAR",
    "pickUpFranchiseCode": "291",
    "dropOffFranchiseCode": "291"
  },
  "companyName": "Ace",
  "companyCode": "AC",
  "debug": false,
  "environment": "Test"
}
```

#### Explicación Parámetros de envío

**1. Rates**

Arreglo con las tarifas solicitadas, contiene los siguientes campos:

| VARIABLE       | SIGNIFICADO                                                                             |
|----------------|-----------------------------------------------------------------------------------------|
| qualifier      | Código con tipo el tipo de tarifa enviado, puede ser RC, IT, CD                         |
| code           | Código de la tarifa                                                                     |
| rate_type_id   | Código del tipo de tarifa, usar best o ver sección [Tarifas](#tarifas) columna "TARIFA" |
| payment_option | Tipo de pago, 1: Prepago, 2: POD, 3: Ambas                                              |
| dummy_iata     | Dummy Iata                                                                              |
| companyIata    | Company Iata                                                                            |
| rateRqmaps     | Arreglo con Rqmaps, puede venir vacío                                                   |
| rateType       | Nombre del tipo tarifa, ver seccion ver sección [Tarifas](#tarifas) columna "NOMBRE"    |
| discountCodes  | Arreglo con códigos de descuento, puede venir vacío                                     |

**2. Credentials**

Todas las credenciales necesarias para la conección al API de la rentadora como url, id, host, password, callerCode, code, etc.

**3. GetDataModel**

| VARIABLE             | SIGNIFICADO                                                                                                                                |
|----------------------|--------------------------------------------------------------------------------------------------------------------------------------------|
| pickUpLocation       | Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.                                                                |
| pickUpAddress        | Nombre de la oficina de pickup (por defecto NA)                                                                                            |
| pickUpFranchiseCode  | Código de la oficina de pickup dado por la rentadora. Para búsquedas fuera de aeropuerto se pueden enviar varios separados por coma `(,)`  |
| dropOffLocation      | Ciudad de Dropoff - Basado en código `IATA` de oficina/aeropuerto de dropoff.                                                              |
| dropOffAddress       | Nombre de la oficina de dropoff (por defecto NA)                                                                                           |
| dropOffFranchiseCode | Código de la oficina de dropoff dado por la rentadora. Para búsquedas fuera de aeropuerto se pueden enviar varios separados por coma `(,)` |
| pickUpDate           | Fecha de Pickup - Formato yyyy-mm-dd                                                                                                       |
| dropOffDate          | Fecha de Dropoff - Formato yyyy-mm-dd                                                                                                      |
| pickUpHour           | Hora de Pickup - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                      |
| dropOffHour          | Hora de Dropoff - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                     |
| cdCode               | Código de descuento                                                                                                                        |
| pcCode               | Código de promoción                                                                                                                        |
| country              | País de oficina, código alfa 2 ej: United States (US), Colombia (CO)                                                                       |
| source               | País de fuente, código alfa 2 ej: United States (US), Colombia (CO)                                                                        |
| rateType             | Código tipo de Tarifa, usar `best` o obtener de [Tarifas](#tarifas)                                                                        |
| paymentType          | Tipo de pago de los resultados mostrados (ppd: Pagar Ahora, pod: Pago en Destino)                                                          |
| lat*                 | Latitud de Pickup                                                                                                                          |
| lng*                 | Longitud de Pickup                                                                                                                         |
| latDropOff*          | Latitud de Dropoff                                                                                                                         |
| lngDropOff*          | Longitud de Dropoff                                                                                                                        |
| sippCode             | Tipo de automóvil según código SIPP                                                                                                        |

*Solo aplica obligatorio en caso de búsqueda fuera de oficinas.

**4. Otros**

| VARIABLE    | SIGNIFICADO                                                                                                       |
|-------------|-------------------------------------------------------------------------------------------------------------------|
| companyName | Nombre de la empresa que alquila el vehículo                                                                      |
| companyCode | Código de la empresa que alquila el vehículo                                                                      |
| debug       | Si se quiere generar los archivos request y response del servicio en la carpeta "files", valores `true` o `false` |
| environment | Entorno en que se esta llamando el servicio, valores posibles `Test` o `Production`                               |

### Respuesta exitosa (status 200)

```
{
    "1": {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": 1,
        "getDataModel": {
            "pickUpLocation": "MIAT01",
            "pickUpAddress": "NA",
            "dropOffLocation": "MIAT01",
            "dropOffAddress": "NA",
            "pickUpDate": "2022-07-18",
            "dropOffDate": "2022-07-25",
            "pickUpHour": "1200",
            "dropOffHour": "1200",
            "cdCode": "NA",
            "pcCode": "NA",
            "country": "US",
            "source": "CO",
            "rateType": "best",
            "lat": "NA",
            "lng": "NA",
            "latDropOff": "NA",
            "lngDropOff": "NA",
            "sippCode": "ECAR",
            "companyCode": "AC",
            "ccrc": "QUM="
        },
        "sippCode": "ECAR",
        "rateIdentifier": "RGZNET",
        "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
        "carModel": "Toyota Yaris",
        "doors": 4,
        "passengers": 4,
        "bags": 2,
        "trans": "Automatic",
        "air": "Yes",
        "payment_option": 1,
        "km_included": "Unlimited Mile",
        "currency": "USD",
        "realBase": 253.96,
        "realTax": 142.27,
        "rateAmount": 396.23,
        "taxNotIncluded": 0,
        "pickUpLocation": "MIAT01",
        "pickUpFranchiseCode": "291",
        "carInfo": {
            "ECAR": {
                "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
                "carModel": "Toyota Yaris",
                "sippCode": "ECAR",
                "km_included": "Unlimited Mile",
                "companyName": "Ace",
                "doors": 4,
                "passengers": 4,
                "bags": 2,
                "air_conditioner": "Yes",
                "transmission": "Automatic",
                "companyCode": "AC",
                "shuttleInfo": ""
            }
        },
        "auxAddRateInformation": true,
        "auxAmadeusNumbers": {
            "NumberCD": "TESTCODE"
        },
        "rateInformation": {
            "VendorRateID": "RGZNET",
            "ReferenceType": "16",
            "ReferenceID": "70049432228",
            "Amount": "396.23",
            "CurrencyCode": "USD",
            "detail": [
                {
                    "amount": 6.04,
                    "comment": "Airport Access Fee"
                },
                {
                    "amount": 20.4,
                    "comment": "CFC Fee"
                },
                {
                    "amount": 8,
                    "comment": "Energy Recovery Fee"
                },
                {
                    "amount": 2.18,
                    "comment": "Miami Privilege Fee"
                },
                {
                    "amount": 8.2,
                    "comment": "State Surcharge"
                },
                {
                    "amount": 7.96,
                    "comment": "Vehicle Licensing Fee"
                },
                {
                    "amount": 7.5,
                    "comment": "Sales Tax"
                }
            ]
        }
    },
    "3": {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": 3,
        "getDataModel": {
            "pickUpLocation": "MIAT01",
            "pickUpAddress": "NA",
            "dropOffLocation": "MIAT01",
            "dropOffAddress": "NA",
            "pickUpDate": "2022-07-18",
            "dropOffDate": "2022-07-25",
            "pickUpHour": "1200",
            "dropOffHour": "1200",
            "cdCode": "NA",
            "pcCode": "NA",
            "country": "US",
            "source": "CO",
            "rateType": "best",
            "lat": "NA",
            "lng": "NA",
            "latDropOff": "NA",
            "lngDropOff": "NA",
            "sippCode": "ECAR",
            "companyCode": "AC",
            "ccrc": "QUM="
        },
        "sippCode": "ECAR",
        "rateIdentifier": "RGZNET",
        "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
        "carModel": "Toyota Yaris",
        "doors": 4,
        "passengers": 4,
        "bags": 2,
        "trans": "Automatic",
        "air": "Yes",
        "payment_option": 1,
        "km_included": "Unlimited Mile",
        "currency": "USD",
        "realBase": 624.4,
        "realTax": 228.1,
        "rateAmount": 852.5,
        "taxNotIncluded": 0,
        "pickUpLocation": "MIAT01",
        "pickUpFranchiseCode": "291",
        "carInfo": {
            "ECAR": {
                "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
                "carModel": "Toyota Yaris",
                "sippCode": "ECAR",
                "km_included": "Unlimited Mile",
                "companyName": "Ace",
                "doors": 4,
                "passengers": 4,
                "bags": 2,
                "air_conditioner": "Yes",
                "transmission": "Automatic",
                "companyCode": "AC",
                "shuttleInfo": ""
            }
        },
        "auxAddRateInformation": true,
        "auxAmadeusNumbers": {
            "NumberCD": "TESTCODE"
        },
        "rateInformation": {
            "VendorRateID": "RGZNET",
            "ReferenceType": "16",
            "ReferenceID": "70049432229",
            "Amount": "852.5",
            "CurrencyCode": "USD",
            "detail": [
                {
                    "amount": 6.04,
                    "comment": "Airport Access Fee"
                },
                {
                    "amount": 20.4,
                    "comment": "CFC Fee"
                },
                {
                    "amount": 8,
                    "comment": "Energy Recovery Fee"
                },
                {
                    "amount": 2.18,
                    "comment": "Miami Privilege Fee"
                },
                {
                    "amount": 8.2,
                    "comment": "State Surcharge"
                },
                {
                    "amount": 7.96,
                    "comment": "Vehicle Licensing Fee"
                },
                {
                    "amount": 7.5,
                    "comment": "Sales Tax"
                }
            ]
        }
    }
}
```

| VARIABLE              | DESCRIPCIÓN                                                                                                                                                                               |
|-----------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| companyName           | Nombre de la empresa que alquila el vehículo                                                                                                                                              |
| companyCode           | Código de la empresa que alquila el vehículo                                                                                                                                              |
| rateType              | Código tipo de Tarifa, usar `best` o obtener de [Tarifas](#tarifas)                                                                                                                       |
| getDataModel          | Información recibida en los parámetros de envío                                                                                                                                           |
| sippCode              | Tipo de automóvil según código SIPP                                                                                                                                                       |
| rateIdentifier        | Código/Identificador de la tarifa                                                                                                                                                         |
| img                   | Imagen del carro                                                                                                                                                                          |
| carModel              | Modelo del carro                                                                                                                                                                          |
| doors                 | Número de puertas                                                                                                                                                                         |
| passengers            | Número de pasajeros                                                                                                                                                                       |
| bags                  | Número de maletas                                                                                                                                                                         |
| trans                 | Tipo de transmisión del carro, valores posibles `Automatic` y `Manual`                                                                                                                    |
| air                   | Si tiene o no aire acondicionado, valores posibles `Yes` y `No`                                                                                                                           |
| payment_option        | Tipo de pago, 1: Prepago, 2: POD, 3: Ambas                                                                                                                                                |
| km_included           | Kilometraje/millaje de la tarifa                                                                                                                                                          |
| currency              | Moneda de la tarifa, ej: USD, COP, EUR...                                                                                                                                                 |
| realBase              | Esta es la base comisionable, el valor sobre el que comisionan                                                                                                                            |
| realTax               | Estos son los impuestos que realmente tiene la tarifa, y no son comisionables                                                                                                             |
| rateAmount            | Total de la tarifa                                                                                                                                                                        |
| taxNotIncluded        | Valor de impuestos no incluidos                                                                                                                                                           |
| pickUpLocation        | Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.                                                                                                               |
| pickUpFranchiseCode   | Código de la oficina de pickup dado por la rentadora. Para búsquedas fuera de aeropuerto se pueden enviar varios separados por coma `(,)`                                                 |
| carInfo               | Contiene la información del carro asociado por el código SIPP                                                                                                                             |
| auxAddRateInformation | Si es necesario llamar a otro servicio cuandos e selecciona una de las tarifas se envia `false`, de lo contrario `true`                                                                   |
| auxAmadeusNumbers     | Si es enviado discountCodes, devolver ese código como NumberCD , si no, no devolver este nodo                                                                                             |
| rateInformation       | Arreglo con datos necesarios de la tarifa, entre los cuales estan detail (un arreglo con descripción y valor de los fees), VendorRateID, Amount, CurrencyCode, ReferenceID, ReferenceType |

### Respuesta con error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```
---

## Servicio Confirmation

Devuelve la confirmación de la rentadora

### URL

- http://localhost/example-rental-company-implementation/web/companies/confirmation

### Parámetros de envío

Se envía un JSON vía `POST`

```
{
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "reservation": {
    "id": 1,
    "first_name": "Albert",
    "last_name": "Test",
    "email": "alberttest123@gmail.com",
    "location_pickup": "MIAT01",
    "location_dropoff": "MIAT01",
    "pickup_date": "2022-07-18",
    "dropoff_date": "2022-07-25",
    "pickup_hour": "1200",
    "dropoff_hour": "1200",
    "additionals": {
      "cbs": {
        "value": 0
      },
      "cst": {
        "value": 0
      },
      "gps": {
        "value": "0"
      },
      "sky": {
        "value": "0"
      }
    },
    "additional_information": {
      "VendorRateID": "RGZNET",
      "Amount": "409.17",
      "ReferenceID": "69138842466",
      "CurrencyCode": "USD",
      "ReferenceType": "16"
    },
    "rate_type_id": "1",
    "sipp_code": "ECAR",
    "company_code": "AC",
    "payment_option": 1
  },
  "debug": false,
  "environment": "Test"
}
```

#### Explicación Parámetros de envío

**1. Credentials**

Todas las credenciales necesarias para la conección al API de la rentadora como url, id, host, password, callerCode, code, etc.

**2. Reservation**

Arreglo con la información de la reserva, contiene los siguientes campos:

| VARIABLE               | SIGNIFICADO                                                                                                                                                                                                              |
|------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| id                     | ID de la reserva                                                                                                                                                                                                         |
| first_name             | Nombre del cliente                                                                                                                                                                                                       |
| last_name              | Apellidos del cliente                                                                                                                                                                                                    |
| email                  | Email del cliente                                                                                                                                                                                                        |
| location_pickup        | Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.                                                                                                                                              |
| location_dropoff       | Ciudad de Dropoff - Basado en código `IATA` de oficina/aeropuerto de dropoff.                                                                                                                                            |
| pickup_date            | Fecha de Pickup - Formato yyyy-mm-dd                                                                                                                                                                                     |
| dropoff_date           | Fecha de Dropoff - Formato yyyy-mm-dd                                                                                                                                                                                    |
| pickup_hour            | Hora de Pickup - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                                                                                                    |
| dropoff_hour           | Hora de Dropoff - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                                                                                                   |
| additionals            | Arreglo con los equipos especiales solicitados por el cliente, donde 0 es que no solicito, de lo contrario regresa el numero de los solicitados, mas información en la sección [Equipos Especiales](#equipos-especiales) |
| additional_information | Arreglo con datos necesarios para la solicitud, entre los cuales estan VendorRateID, Amount, CurrencyCode, ReferenceID, ReferenceType                                                                                    |
| rate_type_id           | Código del tipo de tarifa, usar best o ver sección [Tarifas](#tarifas) columna "TARIFA"                                                                                                                                  |
| sipp_code              | Tipo de automóvil según código SIPP                                                                                                                                                                                      |
| company_code           | Código de la empresa que alquila el vehículo                                                                                                                                                                             |
| payment_option         | Opción de pago de la tarifa ('1'=>'Prepago', '2'=>'Pago en destino', '3'=>'Prepago y pago en destino')                                                                                                                   |

**3. Otros**

| VARIABLE    | SIGNIFICADO                                                                                                       |
|-------------|-------------------------------------------------------------------------------------------------------------------|
| debug       | Si se quiere generar los archivos request y response del servicio en la carpeta "files", valores `true` o `false` |
| environment | Entorno en que se esta llamando el servicio, valores posibles `Test` o `Production`                               |

### Respuesta exitosa (status 200)

```
{
    "rental_confirmation_code": "TESTCODE12991104",
    "amount_confirmed": 409.17,
    "currency_confirmed": "USD",
    "status": "Active",
    "first_name": "Albert",
    "last_name": "Test",
    "email": "alberttest123@gmail.com",
    "location_pickup": "MIAT01",
    "location_dropoff": "MIAT01",
    "pickup_date": "2022-07-18",
    "dropoff_date": "2022-07-25",
    "pickup_hour": "1200",
    "dropoff_hour": "1200",
    "additionals": {
        "cbs": {
            "value": 0
        },
        "cst": {
            "value": 0
        },
        "gps": {
            "value": "0"
        },
        "sky": {
            "value": "0"
        }
    },
    "additional_information": {
      "VendorRateID": "RGZNET",
      "Amount": "409.17",
      "ReferenceID": "69138842466",
      "CurrencyCode": "USD",
      "ReferenceType": "16"
    },
    "rate_type_id": "1",
    "sipp_code": "ECAR",
    "company_code": "AC"
}
```

| VARIABLE                 | SIGNIFICADO                                                                                                                                                                                                              |
|--------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| rental_confirmation_code | Código de confirmación de la reserva, con la rentadora                                                                                                                                                                   |
| amount_confirmed         | Valor de la reserva confirmado por la rentadora                                                                                                                                                                          |
| currency_confirmed       | Moneda de la reserva confirmado por la rentadora                                                                                                                                                                         |
| status                   | Estado de la reserva. Active, OnRequest, Cancel                                                                                                                                                                          |
| first_name               | Nombre del cliente                                                                                                                                                                                                       |
| last_name                | Apellidos del cliente                                                                                                                                                                                                    |
| email                    | Email del cliente                                                                                                                                                                                                        |
| location_pickup          | Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.                                                                                                                                              |
| location_dropoff         | Ciudad de Dropoff - Basado en código `IATA` de oficina/aeropuerto de dropoff.                                                                                                                                            |
| pickup_date              | Fecha de Pickup - Formato yyyy-mm-dd                                                                                                                                                                                     |
| dropoff_date             | Fecha de Dropoff - Formato yyyy-mm-dd                                                                                                                                                                                    |
| pickup_hour              | Hora de Pickup - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                                                                                                    |
| dropoff_hour             | Hora de Dropoff - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                                                                                                   |
| additionals              | Arreglo con los equipos especiales solicitados por el cliente, donde 0 es que no solicito, de lo contrario regresa el numero de los solicitados, mas información en la sección [Equipos Especiales](#equipos-especiales) |
| additional_information   | Arreglo con datos necesarios para la solicitud, entre los cuales estan VendorRateID, Amount, CurrencyCode, ReferenceID, ReferenceType                                                                                    |
| rate_type_id             | Código del tipo de tarifa, usar best o ver sección [Tarifas](#tarifas) columna "TARIFA"                                                                                                                                  |
| sipp_code                | Tipo de automóvil según código SIPP                                                                                                                                                                                      |
| company_code             | Código de la empresa que alquila el vehículo                                                                                                                                                                             |


### Respuesta con error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```
---

## Servicio My Reservation

Consulta los datos de una reserva

### URL

- http://localhost/example-rental-company-implementation/web/companies/my-reservation

### Parámetros de envío

Se envía un JSON vía `POST`

```
{
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "last_name": "Test",
  "rental_confirmation_code": "TESTCODE12991104",
  "raw_response": false,
  "debug": false,
  "environment": "Test"
}
```

#### Explicación Parámetros de envío

**1. Credentials**

Todas las credenciales necesarias para la conección al API de la rentadora como url, id, host, password, callerCode, code, etc.

**2. Otros**

| VARIABLE                 | SIGNIFICADO                                                                                                                                                                                                             |
|--------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| last_name                | Apellidos del cliente                                                                                                                                                                                                   |
| rental_confirmation_code | Código de confirmación de la reserva, con la rentadora                                                                                                                                                                  |
| raw_response             | (Opcional) Si no es enviado o es `true` se regresa la respuesta directa de la rentadora (Ver Respuesta de la rentadora exitosa), si se envía y es `false`, se regresa información de la reserva (Ver Respuesta exitosa) |
| debug                    | Si se quiere generar los archivos request y response del servicio en la carpeta "files", valores `true` o `false`                                                                                                       |
| environment              | Entorno en que se esta llamando el servicio, valores posibles `Test` o `Production`                                                                                                                                     |

### Respuesta exitosa (status 200)

Respuesta de la rentadora en formato JSON (si no se envía el parámetro raw_response o es `false`)

```
{
    "rental_confirmation_code": "TESTCODE12991104",
    "amount_confirmed": 409.17,
    "currency_confirmed": "USD",
    "status": "Active",
    "first_name": "Albert",
    "last_name": "Test",
    "email": "alberttest123@gmail.com",
    "location_pickup": "MIAT01",
    "location_dropoff": "MIAT01",
    "pickup_date": "2022-07-18",
    "pickup_hour": "1200",
    "dropoff_date": "2022-07-25",
    "dropoff_hour": "1200"
}
```

| VARIABLE                   | SIGNIFICADO                                                                                                                                                                                                              |
|----------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| rental_confirmation_code   | Código de confirmación de la reserva, con la rentadora                                                                                                                                                                   |
| amount_confirmed           | Valor de la reserva confirmado por la rentadora                                                                                                                                                                          |
| currency_confirmed         | Moneda de la reserva confirmado por la rentadora                                                                                                                                                                         |
| status                     | Estado de la reserva. Active, OnRequest, Cancel                                                                                                                                                                          |
| first_name *               | Nombre del cliente                                                                                                                                                                                                       |
| last_name  *               | Apellidos del cliente                                                                                                                                                                                                    |
| email *                    | Email del cliente                                                                                                                                                                                                        |
| location_pickup *          | Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.                                                                                                                                              |
| location_dropoff *         | Ciudad de Dropoff - Basado en código `IATA` de oficina/aeropuerto de dropoff.                                                                                                                                            |
| pickup_date *              | Fecha de Pickup - Formato yyyy-mm-dd                                                                                                                                                                                     |
| dropoff_date *             | Fecha de Dropoff - Formato yyyy-mm-dd                                                                                                                                                                                    |
| pickup_hour *              | Hora de Pickup - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                                                                                                    |
| dropoff_hour *             | Hora de Dropoff - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                                                                                                                                                   |

* Estos campos son opcionales, dependiendo si la respuesta de la rentadora los regresa

### Respuesta de la rentadora exitosa (status 200)

Respuesta de la rentadora en formato JSON (parámetro raw_response `true`)

```
{
    "soapBody": {
        "OTA_VehRetResRS": {
            "@attributes": {
                "TimeStamp": "2022-06-22T13:56:31.3362548-04:00",
                "Target": "Test",
                "Version": "5.0"
            },
            "Success": {},
            "VehRetResRSCore": {
                "VehReservation": {
                    "Customer": {
                        "Primary": {
                            "PersonName": {
                                "GivenName": "Name",
                                "Surname": "Test"
                            },
                            "Email": "nametest@gmail.com"
                        }
                    },
                    "VehSegmentCore": {
                        "ConfID": {
                            "@attributes": {
                                "Type": "14",
                                "ID": "TESTCODE12991104"
                            }
                        },
                        "Vendor": "ACE Rent A Car",
                        "VehRentalCore": {
                            "@attributes": {
                                "PickUpDateTime": "2022-07-18T12:00:00",
                                "ReturnDateTime": "2022-07-25T12:00:00"
                            },
                            "PickUpLocation": {
                                "@attributes": {
                                    "LocationCode": "MIAT01",
                                    "CodeContext": "AC"
                                }
                            },
                            "ReturnLocation": {
                                "@attributes": {
                                    "LocationCode": "MIAT01",
                                    "CodeContext": "AC"
                                }
                            }
                        },
                        "Vehicle": {
                            "@attributes": {
                                "AirConditionInd": "true",
                                "TransmissionType": "Automatic",
                                "FuelType": "Unspecified",
                                "DriveType": "Unspecified",
                                "PassengerQuantity": "4",
                                "BaggageQuantity": "2",
                                "Code": "ECAR",
                                "CodeContext": "SIPP"
                            },
                            "VehType": {
                                "@attributes": {
                                    "VehicleCategory": "1",
                                    "DoorCount": "2-4"
                                }
                            },
                            "VehClass": {
                                "@attributes": {
                                    "Size": "3"
                                }
                            },
                            "VehMakeModel": {
                                "@attributes": {
                                    "Name": "Toyota Yaris"
                                }
                            },
                            "PictureURL": "Toyota Yaris.png"
                        },
                        "RentalRate": {
                            "RateDistance": {
                                "@attributes": {
                                    "Unlimited": "true",
                                    "DistUnitName": "Mile",
                                    "VehiclePeriodUnitName": "RentalPeriod"
                                }
                            },
                            "VehicleCharges": {
                                "VehicleCharge": {
                                    "@attributes": {
                                        "CurrencyCode": "USD",
                                        "Amount": "264.46",
                                        "Description": "Daily Rate",
                                        "IncludedInEstTotalInd": "true",
                                        "Purpose": "1"
                                    },
                                    "Calculation": {
                                        "@attributes": {
                                            "UnitCharge": "37.78",
                                            "UnitName": "Day",
                                            "Quantity": "7"
                                        }
                                    }
                                }
                            },
                            "RateQualifier": {
                                "@attributes": {
                                    "RateCategory": "16",
                                    "PromotionCode": "RGZNET",
                                    "RateQualifier": "NET2",
                                    "RatePeriod": "Daily"
                                }
                            },
                            "RateRestrictions": {
                                "@attributes": {
                                    "GuaranteeReqInd": "true"
                                }
                            }
                        },
                        "Fees": {
                            "Fee": [
                                {
                                    "@attributes": {
                                        "CurrencyCode": "USD",
                                        "Amount": "29.38",
                                        "Description": "Airport Access Fee",
                                        "IncludedInEstTotalInd": "true",
                                        "Purpose": "6"
                                    },
                                    "Calculation": {
                                        "@attributes": {
                                            "UnitCharge": "264.46",
                                            "Percentage": "11.11"
                                        }
                                    }
                                },
                                {
                                    "@attributes": {
                                        "CurrencyCode": "USD",
                                        "Amount": "35.70",
                                        "Description": "CFC Fee",
                                        "IncludedInEstTotalInd": "true",
                                        "Purpose": "6"
                                    },
                                    "Calculation": {
                                        "@attributes": {
                                            "UnitCharge": "5.10",
                                            "UnitName": "Day",
                                            "Quantity": "7"
                                        }
                                    }
                                },
                                {
                                    "@attributes": {
                                        "CurrencyCode": "USD",
                                        "Amount": "14.00",
                                        "Description": "Energy Recovery Fee",
                                        "IncludedInEstTotalInd": "true",
                                        "Purpose": "6"
                                    },
                                    "Calculation": {
                                        "@attributes": {
                                            "UnitCharge": "2.00",
                                            "UnitName": "Day",
                                            "Quantity": "7"
                                        }
                                    }
                                },
                                {
                                    "@attributes": {
                                        "CurrencyCode": "USD",
                                        "Amount": "10.58",
                                        "Description": "Miami Privilege Fee",
                                        "IncludedInEstTotalInd": "true",
                                        "Purpose": "7"
                                    },
                                    "Calculation": {
                                        "@attributes": {
                                            "UnitCharge": "264.46",
                                            "Percentage": "4.0"
                                        }
                                    }
                                },
                                {
                                    "@attributes": {
                                        "CurrencyCode": "USD",
                                        "Amount": "14.35",
                                        "Description": "State Surcharge",
                                        "IncludedInEstTotalInd": "true",
                                        "Purpose": "6"
                                    },
                                    "Calculation": {
                                        "@attributes": {
                                            "UnitCharge": "2.05",
                                            "UnitName": "Day",
                                            "Quantity": "7"
                                        }
                                    }
                                },
                                {
                                    "@attributes": {
                                        "CurrencyCode": "USD",
                                        "Amount": "13.93",
                                        "Description": "Vehicle Licensing Fee",
                                        "IncludedInEstTotalInd": "true",
                                        "Purpose": "6"
                                    },
                                    "Calculation": {
                                        "@attributes": {
                                            "UnitCharge": "1.99",
                                            "UnitName": "Day",
                                            "Quantity": "7"
                                        }
                                    }
                                },
                                {
                                    "@attributes": {
                                        "CurrencyCode": "USD",
                                        "Amount": "26.77",
                                        "Description": "Sales Tax",
                                        "IncludedInEstTotalInd": "true",
                                        "Purpose": "7"
                                    },
                                    "Calculation": {
                                        "@attributes": {
                                            "UnitCharge": "382.40",
                                            "Percentage": "7.0"
                                        }
                                    }
                                }
                            ]
                        },
                        "TotalCharge": {
                            "@attributes": {
                                "RateTotalAmount": "264.46",
                                "EstimatedTotalAmount": "409.17",
                                "CurrencyCode": "USD"
                            }
                        }
                    },
                    "VehSegmentInfo": {
                        "PaymentRules": {
                            "PaymentRule": {
                                "@attributes": {
                                    "CurrencyCode": "USD",
                                    "Amount": "409.17",
                                    "RuleType": "2",
                                    "PaymentType": "3"
                                }
                            }
                        },
                        "LocationDetails": {
                            "@attributes": {
                                "AtAirport": "true",
                                "Code": "MIAT01",
                                "Name": "Miami International Airport",
                                "CodeContext": "AC",
                                "AssocAirportLocList": "MIA"
                            },
                            "Address": {
                                "StreetNmbr": "3900 NW 25th St",
                                "CityName": "Miami",
                                "PostalCode": "33142",
                                "StateProv": "Florida",
                                "CountryName": "United States"
                            },
                            "Telephone": [
                                {
                                    "@attributes": {
                                        "PhoneTechType": "1",
                                        "PhoneNumber": "786-656-4200",
                                        "DefaultInd": "true"
                                    }
                                },
                                {
                                    "@attributes": {
                                        "PhoneTechType": "3",
                                        "PhoneNumber": "fax"
                                    }
                                }
                            ]
                        }
                    }
                }
            }
        }
    }
}
```

### Respuesta con error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```
---

## Servicio Cancel

Servicio para solicitar la cancelación de una reserva

### URL

- http://localhost/example-rental-company-implementation/web/companies/cancel

### Parámetros de envío

Se envía un JSON vía `POST`

```
{
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "id": 1,
  "last_name": "Test",
  "rental_confirmation_code": "TESTCODE12991104",
  "debug": false,
  "environment": "Test"
}
```

#### Explicación Parámetros de envío

**1. Credentials**

Todas las credenciales necesarias para la conección al API de la rentadora como url, id, host, password, callerCode, code, etc.

**2. Otros**

| VARIABLE                 | SIGNIFICADO                                                                                                       |
|--------------------------|-------------------------------------------------------------------------------------------------------------------|
| id                       | ID de la reserva                                                                                                  |
| last_name                | Apellidos del cliente                                                                                             |
| rental_confirmation_code | Código de confirmación de la reserva, con la rentadora                                                            |
| debug                    | Si se quiere generar los archivos request y response del servicio en la carpeta "files", valores `true` o `false` |
| environment              | Entorno en que se esta llamando el servicio, valores posibles `Test` o `Production`                               |

### Respuesta exitosa (status 200)

Respuesta de la rentadora en formato JSON en caso de que si fue cancelada, de lo contrario devolver `Respuesta con error (status 500)` con el mensaje de la razón por la cual no se pudo hacer

```
{
    "success": "RESERVATION_CANCELLED"
}
```

### Respuesta con error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```

## Servicio Get Offices

Servicio que busca las oficinas disponibles de la rentadora

### URL

- http://localhost/example-rental-company-implementation/web/companies/get-offices

### Parámetros de envío

Se envía un JSON vía `POST`

```
{
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "countryCode": "US",
  "companyName": "Ace",
  "companyCode": "AC",
  "debug": false,
  "environment": "Test"
}
```

#### Explicación Parámetros de envío

**1. Credentials**

Todas las credenciales necesarias para la conección al API de la rentadora como url, id, host, password, callerCode, code, etc.

**2. Otros**

| VARIABLE    | SIGNIFICADO                                                                                                       |
|-------------|-------------------------------------------------------------------------------------------------------------------|
| countryCode | País, código alfa 2 ej: United States (US), Colombia (CO)                                                         |
| companyName | Nombre de la empresa que alquila el vehículo                                                                      |
| companyCode | Código de la empresa que alquila el vehículo                                                                      |
| debug       | Si se quiere generar los archivos request y response del servicio en la carpeta "files", valores `true` o `false` |
| environment | Entorno en que se esta llamando el servicio, valores posibles `Test` o `Production`                               |

### Respuesta exitosa (status 200)

```
[
    {
        "office_code": "ABQT01",
        "company_name": "Ace",
        "company_code": "AC",
        "status": 1,
        "address": "Albuquerque International Sunport, 3400 University Blvd SE, Albuquerque",
        "lat": "",
        "lng": "",
        "zip_code": "87106",
        "city_name": "Albuquerque",
        "state": "NM",
        "country_code": "US",
        "franchise_code": "ABQT01",
        "additional_information": {
            "AtAirport": "true",
            "Code": "ABQT01",
            "Name": "Albuquerque International Sunport",
            "CodeContext": "AC",
            "AssocAirportLocList": "ABQ",
            "phone": "505-437-5809"
        },
        "schedule": {
            "1": [
                {
                    "opening": "0700",
                    "close": "2100"
                }
            ],
            "2": [
                {
                    "opening": "0700",
                    "close": "2100"
                }
            ],
            "3": [
                {
                    "opening": "0700",
                    "close": "2100"
                }
            ],
            "4": [
                {
                    "opening": "0700",
                    "close": "2100"
                }
            ],
            "5": [
                {
                    "opening": "0700",
                    "close": "2100"
                }
            ],
            "6": [
                {
                    "opening": "0800",
                    "close": "1800"
                }
            ],
            "7": [
                {
                    "opening": "0800",
                    "close": "1800"
                }
            ]
        },
        "shuttle_info": "2",
        "iata": "ABQ"
    },
    ...
    {
        "office_code": "AGHC01",
        "company_name": "Ace",
        "company_code": "AC",
        "status": 1,
        "address": "Ängelholm Hedin Bil, Midgårdsgatan 11, Angelholm",
        "lat": "",
        "lng": "",
        "zip_code": "262 71",
        "city_name": "Angelholm",
        "state": "",
        "country_code": "US",
        "franchise_code": "AGHC01",
        "additional_information": {
            "AtAirport": "false",
            "Code": "AGHC01",
            "Name": "Ängelholm Hedin Bil",
            "CodeContext": "AC",
            "phone": "0046 0431-169 19"
        },
        "schedule": {
            "1": [
                {
                    "opening": "0800",
                    "close": "1600"
                }
            ],
            "2": [
                {
                    "opening": "0800",
                    "close": "1600"
                }
            ],
            "3": [
                {
                    "opening": "0800",
                    "close": "1600"
                }
            ],
            "4": [
                {
                    "opening": "0800",
                    "close": "1600"
                }
            ],
            "5": [
                {
                    "opening": "0800",
                    "close": "1600"
                }
            ]
        },
        "shuttle_info": null,
        "iata": null
    }
]
```

| VARIABLE               | SIGNIFICADO                                                                                                                                       |
|------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------|
| office_code            | Código `IATA` de oficina de 6 letras, ej: MIAE08, MIAC72                                                                                          |
| company_name           | Nombre de la empresa que alquila el vehículo                                                                                                      |
| company_code           | Código de la empresa que alquila el vehículo                                                                                                      |
| status                 | Estado de la oficina, `1` si es una oficina activa, de lo contrario `0`                                                                           |
| lat                    | Latitud de la oficina                                                                                                                             |
| lng                    | Longitud de la oficina                                                                                                                            |
| zip_code               | Código postal                                                                                                                                     |
| city_name              | Nombre de la ciudad                                                                                                                               |
| state                  | Código `IATA` del Estado donde se encuentra la oficina de 2 letras                                                                                |
| country_code           | País de oficina, código alfa 2 ej: United States (US), Colombia (CO)                                                                              |
| franchise_code         | Código de la oficina devuelto por la rentadora                                                                                                    |
| additional_information | Arreglo con información adicional, como AtAirport, phone, etc.                                                                                    |
| schedule               | El horario contiene un arreglo de días (iniciando el lunes con código 1 finalizando el domingo con código 7) y sus respectivas horas de apertura. |
| shuttle_info           | Recogida para oficinas en el aeropuerto, 1: En terminal, 2: Servicio de transporte (shuttle), 3: Meet and Greet                                   |
| iata                   | Código `IATA` del aeropuerto para oficinas en el aeropuerto                                                                                       |

### Respuesta con error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```
---

## Tarifas

| TARIFA | NOMBRE                | DESCRIPCIÓN                                                                                                                                                                                                        |
|--------|-----------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1      | just_car              | Sólo Carro, Tarifa Básica con Kilometraje Ilimitado, Impuestos y Sobrecargos Obligatorios                                                                                                                          |
| 2      | basic_protection      | Protección Básica, Proteccion de Colision o Perdida (CDW/LDW) sin Franquicia, Kilometraje Ilimitado, Impuestos y Sobrecargos Obligatorios                                                                          |
| 3      | full_protection       | Protección Total, Proteccion de Colision o Perdida (CDW/LDW) sin Franquicia, Daños a Terceros, Un Conductor Adicional, Kilometraje Ilimitado, Impuestos y Sobrecargos Obligatorios                                 |
| 4      | full_protection_+_gps | Protección Total + GPS, GPS, Proteccion de Colision o Perdida (CDW/LDW) sin Franquicia, Daños a Terceros, Un Conductor Adicional, Kilometraje Ilimitado, Impuestos y Sobrecargos Obligatorios                      |
| 5      | full_protection_+_gas | Protección Total + Gas, Un Tanque de Combustible, Kilometraje Ilimitado, Proteccion de Colision o Perdida (CDW/LDW) Sin Franquicia, Daños a Terceros, Un Conductor Adicional, Impuestos y Sobrecargos Obligatorios |
| 6      | all_inclusive         | Todo Incluido, GPS, Un Tanque de Combustible, Kilometraje Ilimitado, Proteccion de Colision o Perdida (CDW/LDW) sin Franquicia, Daños a Terceros, Un Conductor Adicional, Impuestos y Sobrecargos Obligatorios     |
| 7      | basic_protection_eu   | Protección Básica EU, Protección de Colisión con Franquicia, Protección de Robo, Impuestos e Sobrecargos Obligatorios                                                                                              |
| 8      | super_protection      | Super Protección, Protección contra Colisión sin Deducible, Protección contra Robo, Impuestos y Cargos Obligatorios.                                                                                               |

## Equipos Especiales

| VARIABLE | DESCRIPCIÓN                                                                                |
|----------|--------------------------------------------------------------------------------------------|
| cbs      | Child booster seat, asiento para niños 2-5 años                                            |
| cst      | Child toddler seat, asiento para niños 0-2 años                                            |
| gps      | GPS, SOLO si en la tarifa no se incluye (no todos los vehiculos tienen esta opcion valida) |
| sky      | Sky Racks, SOLO para los lugares validos                                                   |

## Postman

* Descargue demo en postman [aquí](example-rental-company-implementation-postman_collection.json).

