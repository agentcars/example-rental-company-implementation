# Ejemplo de implementación de una rentadora de autos

- [Servicio Get Matrix](#servicio-get-matrix)
- [Servicio Get Selection](#servicio-get-selection)

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
    "lngDropOff": "NA"
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

|VARIABLE       |SIGNIFICADO                                                                                |
|---------------|-------------------------------------------------------------------------------------------|
|qualifier      |Código con tipo el tipo de tarifa enviado, puede ser RC, IT, CD                            |
|code           |Código de la tarifa                                                                        |
|rate_type_id   |Código del tipo de tarifa, usar best o ver sección [Tarifas](#tarifas) columna "TARIFA"    |
|payment_option |Tipo de pago, 1: Prepago, 2: POD, 3: Ambas                                                 |
|dummy_iata     |Dummy Iata                                                                                 |
|companyIata    |Company Iata                                                                               |
|rateRqmaps     |Arreglo con Rqmaps, puede venir vacío                                                      |
|rateType       |Nombre del tipo tarifa, ver seccion ver sección [Tarifas](#tarifas) columna "NOMBRE"       |
|discountCodes  |Arreglo con códigos de descuento, puede venir vacío                                        |

**2. Credentials**

Todas las credenciales necesarias para la conección al API de la rentadora como url, id, host, password, callerCode, code, etc.

**3. GetDataModel**

|VARIABLE         |SIGNIFICADO                                                                          |
|-----------------|-------------------------------------------------------------------------------------|
|pickUpLocation   |Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.          |
|pickUpAddress    |Nombre de la oficina de pickup (por defecto NA)                                      |
|dropOffLocation  |Ciudad de Dropoff - Basado en código `IATA` de oficina/aeropuerto de dropoff.        |
|dropOffAddress   |Nombre de la oficina de dropoff (por defecto NA)                                     |
|pickUpDate       |Fecha de Pickup - Formato yyyy-mm-dd                                                 |
|dropOffDate      |Fecha de Dropoff - Formato yyyy-mm-dd                                                |
|pickUpHour       |Hora de Pickup - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                |
|dropOffHour      |Hora de Dropoff - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm               |
|cdCode           |Código de descuento                                                                  |
|pcCode           |Código de promoción                                                                  |
|country          |País de oficina, código alfa 2 ej: United States (US), Colombia (CO)                 |
|source           |País de fuente, código alfa 2 ej: United States (US), Colombia (CO)                  |
|rateType         |Código tipo de Tarifa, usar `best` o obtener de [Tarifas](rates.md)                  |
|paymentType      |Tipo de pago de los resultados mostrados (ppd: Pagar Ahora, pod: Pago en Destino)    |
|lat*             |Latitud de Pickup                                                                    |
|lng*             |Longitud de Pickup                                                                   |
|latDropOff*      |Latitud de Dropoff                                                                   |
|lngDropOff*      |Longitud de Dropoff                                                                  |

*Solo aplica obligatorio en caso de búsqueda fuera de oficinas.

**4. Otros**

|VARIABLE       |SIGNIFICADO                                                                                                        |
|---------------|-------------------------------------------------------------------------------------------------------------------|
|companyName    |Nombre de la empresa que alquila el vehículo                                                                       |
|companyCode    |Código de la empresa que alquila el vehículo                                                                       |
|debug          |Si se quiere generar los archivos request y response del servicio en la carpeta "files", valores `true` o `false`  |
|environment    |Entorno en que se esta llamando el servicio, valores posibles `Test` o `Production`                                |

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
        "taxNotIncluded": 0
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
        "taxNotIncluded": 0
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
        "taxNotIncluded": 0
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
        "taxNotIncluded": 0
    },
]
```

| VARIABLE        | DESCRIPCIÓN                                                                             |
|-----------------|-----------------------------------------------------------------------------------------|
|rateType         | Código del tipo de tarifa, usar best o ver sección [Tarifas](#tarifas) columna "TARIFA" |
|air              | Si tiene o no aire acondicionado, valores posibles `Yes` y `No`                         |
|trans            | Tipo de transmisión del carro, valores posibles `Automatic` y `Manual`                  |
|passengers       | Número de pasajeros                                                                     |
|bags             | Número de maletas                                                                       |
|sippCode         | Tipo de automóvil según código SIPP                                                     |
|doors            | Número de puertas                                                                       |
|img              | Imagen del carro                                                                        |
|carModel         | Modelo del carro                                                                        |
|km_included      | Kilometraje/millaje de la tarifa                                                        |
|payment_option   | Tipo de pago, 1: Prepago, 2: POD, 3: Ambas                                              |
|currency         | Moneda de la tarifa, ej: USD, COP, EUR...                                               |
|realBase         | Esta es la base comisionable, el valor sobre el que comisionan                          |
|realTax          | Estos son los impuestos que realmente tiene la tarifa, y no son comisionables           |
|rateAmount       | total de la tarifa                                                                      |
|taxNotIncluded   | Valor de impuestos no incluidos                                                         |

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
    "sippCode": "ECAR"
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

|VARIABLE       |SIGNIFICADO                                                                                |
|---------------|-------------------------------------------------------------------------------------------|
|qualifier      |Código con tipo el tipo de tarifa enviado, puede ser RC, IT, CD                            |
|code           |Código de la tarifa                                                                        |
|rate_type_id   |Código del tipo de tarifa, usar best o ver sección [Tarifas](#tarifas) columna "TARIFA"    |
|payment_option |Tipo de pago, 1: Prepago, 2: POD, 3: Ambas                                                 |
|dummy_iata     |Dummy Iata                                                                                 |
|companyIata    |Company Iata                                                                               |
|rateRqmaps     |Arreglo con Rqmaps, puede venir vacío                                                      |
|rateType       |Nombre del tipo tarifa, ver seccion ver sección [Tarifas](#tarifas) columna "NOMBRE"       |
|discountCodes  |Arreglo con códigos de descuento, puede venir vacío                                        |

**2. Credentials**

Todas las credenciales necesarias para la conección al API de la rentadora como url, id, host, password, callerCode, code, etc.

**3. GetDataModel**

|VARIABLE         |SIGNIFICADO                                                                          |
|-----------------|-------------------------------------------------------------------------------------|
|pickUpLocation   |Ciudad de Pickup - Basado en código `IATA` de oficina/aeropuerto de pickup.          |
|pickUpAddress    |Nombre de la oficina de pickup (por defecto NA)                                      |
|dropOffLocation  |Ciudad de Dropoff - Basado en código `IATA` de oficina/aeropuerto de dropoff.        |
|dropOffAddress   |Nombre de la oficina de dropoff (por defecto NA)                                     |
|pickUpDate       |Fecha de Pickup - Formato yyyy-mm-dd                                                 |
|dropOffDate      |Fecha de Dropoff - Formato yyyy-mm-dd                                                |
|pickUpHour       |Hora de Pickup - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm                |
|dropOffHour      |Hora de Dropoff - Formato militar ej: 0800 -> 8:00 am, 1600 -> 04:00pm               |
|cdCode           |Código de descuento                                                                  |
|pcCode           |Código de promoción                                                                  |
|country          |País de oficina, código alfa 2 ej: United States (US), Colombia (CO)                 |
|source           |País de fuente, código alfa 2 ej: United States (US), Colombia (CO)                  |
|rateType         |Código tipo de Tarifa, usar `best` o obtener de [Tarifas](rates.md)                  |
|paymentType      |Tipo de pago de los resultados mostrados (ppd: Pagar Ahora, pod: Pago en Destino)    |
|lat*             |Latitud de Pickup                                                                    |
|lng*             |Longitud de Pickup                                                                   |
|latDropOff*      |Latitud de Dropoff                                                                   |
|lngDropOff*      |Longitud de Dropoff                                                                  |
|sippCode         |Tipo de automóvil según código SIPP                                                  |

*Solo aplica obligatorio en caso de búsqueda fuera de oficinas.

**4. Otros**

|VARIABLE       |SIGNIFICADO                                                                                                        |
|---------------|-------------------------------------------------------------------------------------------------------------------|
|companyName    |Nombre de la empresa que alquila el vehículo                                                                       |
|companyCode    |Código de la empresa que alquila el vehículo                                                                       |
|debug          |Si se quiere generar los archivos request y response del servicio en la carpeta "files", valores `true` o `false`  |
|environment    |Entorno en que se esta llamando el servicio, valores posibles `Test` o `Production`                                |

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
        "category": "Others",
        "carModel": "Toyota Yaris",
        "doors": 4,
        "passengers": 4,
        "bags": 2,
        "trans": "Automatic",
        "air": "Yes",
        "payment_option": 1,
        "km_included": "Mile",
        "currency": "USD",
        "realBase": 253.96,
        "realTax": 142.27,
        "rateAmount": 396.23,
        "taxNotIncluded": 0,
        "carInfo": {
            "ECAR": {
                "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
                "carModel": "Toyota Yaris",
                "sippCode": "ECAR",
                "km_included": "Mile",
                "companyName": "Ace",
                "doors": 4,
                "passengers": 4,
                "bags": 2,
                "air_conditioner": "Yes",
                "transmission": "Automatic",
                "categoryName": "Others",
                "companyCode": "AC",
                "shuttleInfo": ""
            }
        },
        "auxAddRateInformation": true
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
        "category": "Others",
        "carModel": "Toyota Yaris",
        "doors": 4,
        "passengers": 4,
        "bags": 2,
        "trans": "Automatic",
        "air": "Yes",
        "payment_option": 1,
        "km_included": "Mile",
        "currency": "USD",
        "realBase": 624.4,
        "realTax": 228.1,
        "rateAmount": 852.5,
        "taxNotIncluded": 0,
        "carInfo": {
            "ECAR": {
                "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
                "carModel": "Toyota Yaris",
                "sippCode": "ECAR",
                "km_included": "Mile",
                "companyName": "Ace",
                "doors": 4,
                "passengers": 4,
                "bags": 2,
                "air_conditioner": "Yes",
                "transmission": "Automatic",
                "categoryName": "Others",
                "companyCode": "AC",
                "shuttleInfo": ""
            }
        },
        "auxAddRateInformation": true
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

## Confirmation

Returns information of the confirmation

### URL

- http://localhost/example-rental-company-implementation/web/companies/confirmation

### Request Parameters

Method POST

```
{
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "reservation": {
    "first_name": "Name",
    "last_name": "Test",
    "email": "nametest@gmail.com",
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
      "csi": {
        "value": 0
      },
      "cst": {
        "value": 0
      },
      "dvd": {
        "value": 0
      },
      "gps": {
        "value": "0"
      },
      "pax": {
        "value": "4"
      },
      "sky": {
        "value": "0"
      },
      "bags": {
        "value": "2"
      },
      "doors": {
        "value": "4"
      },
      "rateInfo": {
        "id": "1",
        "name": "Just Car",
        "alias": "Just Car",
        "order": "0",
        "inclusions": {
          "mileage": {
            "icon": "icon icon-unlimited-miles",
            "alias": "Mileage",
            "limit": "Unlimited Miles",
            "description": "Includes these kilometers/miles."
          },
          "surcharges": {
            "icon": "icon icon-tax",
            "alias": "Tax",
            "description": "Includes all the mandatory taxes and surcharges to make your trip easier."
          }
        },
        "description": "Basic Rate with Mandatory Taxes and Fees"
      },
      "satelite": {
        "value": 0
      },
      "air_conditioner": {
        "value": "Yes"
      }
    },
    "additional_information": {
      "cid": "",
      "hash": "12345678901234567890123456789012",
      "Amount": "409.17",
      "is_net": 1,
      "source": "CO",
      "uniqid": "9876543210987",
      "referrer": "http://localhost/cars-yii2/frontend/web/es/site/reservation/?companyCode=AC&sippCode=ECAR&pickUpLocation=MIA&dropOffLocation=MIA&pickUpDate=2022-07-18&dropOffDate=2022-07-25&pickUpHour=1200&dropOffHour=1200&ccrc=QUM%3D&rateType=1&pickUpAddress=NA&dropOffAddress=NA&country=US&source=CO&cdCode=NA&pcCode=NA&tp=1",
      "session_id": "19734651937857946132851973",
      "ReferenceID": "69138842466",
      "shuttleInfo": "On Terminal",
      "CurrencyCode": "USD",
      "ReferenceType": "16",
      "newRefundPolicy": true,
      "shuttleDescription": [],
      "destination_country": "US"
    },
    "rate_type_id": "1",
    "sipp_code": "ECAR",
    "company_code": "AC"
  },
  "debug": false,
  "environment": "Test"
}
```

### Response success (status 200)

```
{
    "rental_confirmation_code": "TESTCODE12991104",
    "amount_confirmed": 409.17,
    "currency_confirmed": "USD"
}
```

### Response with error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```

## My Reservation

Returns information of the reservation

### URL

- http://localhost/example-rental-company-implementation/web/companies/my-reservation

### Request Parameters

Method POST

```
{
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "lastName": "Test",
  "confirmationCode": "TESTCODE12991104",
  "debug": false,
  "environment": "Test"
}
```

### Response success (status 200)

Response of the rental service

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

### Response with error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```

## Cancel

Cancel reservation

### URL

- http://localhost/example-rental-company-implementation/web/companies/cancel

### Request Parameters

Method POST

```
{
  "credentials": {
    "url": URL,
    "id": ID,
    "host": HOST
  },
  "lastName": "Test",
  "confirmationCode": "TESTCODE12991104",
  "debug": false,
  "environment": "Test"
}
```

### Response success (status 200)

Response of the rental service

```
{
    "soapBody": {
        "OTA_VehCancelRS": {
            "@attributes": {
                "TimeStamp": "2022-06-22T14:16:58.4942195-04:00",
                "Target": "Test",
                "Version": "5.0"
            },
            "Success": {},
            "VehCancelRSCore": {
                "@attributes": {
                    "CancelStatus": "Cancelled"
                }
            },
            "VehCancelRSInfo": {
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

### Response with error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```

## Get Offices

Returns Offices information

### URL

- http://localhost/example-rental-company-implementation/web/companies/get-offices

### Request Parameters

Method POST

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

### Response success (status 200)

```
[
    {
        "office_code": "ABQT01",
        "update_office": 1,
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
            "phone": "505-437-5809",
            "updateDate": "2022-06-23 16:06:46"
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
        "update_office": 1,
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
            "phone": "0046 0431-169 19",
            "updateDate": "2022-06-23 16:06:46"
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

### Response with error (status 500)

```
{
    "name": "Internal Server Error",
    "message": "Empty response",
    "code": 0,
    "status": 500,
    "type": "yii\\web\\HttpException"
}
```

## Tarifas
|TARIFA|NOMBRE                 |DESCRIPCIÓN                                                                                                                                                                                                         |
|------|-----------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
|  1   | just_car              | Sólo Carro, Tarifa Básica con Kilometraje Ilimitado, Impuestos y Sobrecargos Obligatorios                                                                                                                          |
|  2   | basic_protection      | Protección Básica, Proteccion de Colision o Perdida (CDW/LDW) sin Franquicia, Kilometraje Ilimitado, Impuestos y Sobrecargos Obligatorios                                                                          |
|  3   | full_protection       | Protección Total, Proteccion de Colision o Perdida (CDW/LDW) sin Franquicia, Daños a Terceros, Un Conductor Adicional, Kilometraje Ilimitado, Impuestos y Sobrecargos Obligatorios                                 |
|  4   | full_protection_+_gps | Protección Total + GPS, GPS, Proteccion de Colision o Perdida (CDW/LDW) sin Franquicia, Daños a Terceros, Un Conductor Adicional, Kilometraje Ilimitado, Impuestos y Sobrecargos Obligatorios                      |
|  5   | full_protection_+_gas | Protección Total + Gas, Un Tanque de Combustible, Kilometraje Ilimitado, Proteccion de Colision o Perdida (CDW/LDW) Sin Franquicia, Daños a Terceros, Un Conductor Adicional, Impuestos y Sobrecargos Obligatorios |
|  6   | all_inclusive         | Todo Incluido, GPS, Un Tanque de Combustible, Kilometraje Ilimitado, Proteccion de Colision o Perdida (CDW/LDW) sin Franquicia, Daños a Terceros, Un Conductor Adicional, Impuestos y Sobrecargos Obligatorios     |
|  7   | basic_protection_eu   | Protección Básica EU, Protección de Colisión con Franquicia, Protección de Robo, Impuestos e Sobrecargos Obligatorios                                                                                              |
|  8   | super_protection      | Super Protección, Protección contra Colisión sin Deducible, Protección contra Robo, Impuestos y Cargos Obligatorios.                                                                                               |