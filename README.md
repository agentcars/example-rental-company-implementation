# Example rental company implementation


## Get Matrix

Returns all the available rates of the company according to the parameters sent

### URL

- http://localhost/example-rental-company-implementation/web/companies/get-matrix

### Request Parameters

Method POST

```
{
  "rates": [
    {
      "id": "1",
      "qualifier": "RC",
      "code": "RGZNET",
      "company_id": "34",
      "rate_type_id": "1",
      "payment_option": "1",
      "destination_coverage": null,
      "airport_id": "9999",
      "country_id": "254",
      "status": "1",
      "rate_term_id": "1",
      "entry_rate_type_id": "12",
      "commission_type_id": "2",
      "commission": "25",
      "franchise_id": null,
      "currency": null,
      "location_list": null,
      "source_coverage": "1",
      "dynamic_rate": null,
      "dummy_iata": "",
      "type_rental_cover": null,
      "sites": "[\"all\"]",
      "rateName": "Just Car",
      "companyCode": "AC",
      "companyName": "ACE",
      "companyCid": "",
      "companyIata": "12345678",
      "orders": "0",
      "inclusions": "[1, 2]",
      "rateRqmaps": [],
      "rateInclusions": [],
      "rateType": "just_car",
      "discountCodes": []
    },
    {
      "id": "2",
      "qualifier": "RC",
      "code": "RGZNET",
      "company_id": "34",
      "rate_type_id": "3",
      "payment_option": "1",
      "destination_coverage": null,
      "airport_id": "9999",
      "country_id": "254",
      "status": "1",
      "rate_term_id": "1",
      "entry_rate_type_id": "12",
      "commission_type_id": "2",
      "commission": "25",
      "franchise_id": null,
      "currency": null,
      "location_list": null,
      "source_coverage": "1",
      "dynamic_rate": null,
      "dummy_iata": "",
      "type_rental_cover": null,
      "sites": "[\"all\"]",
      "rateName": "Full Protection",
      "companyCode": "AC",
      "companyName": "ACE",
      "companyCid": "",
      "companyIata": "12345678",
      "orders": "2",
      "inclusions": "[\"1\", \"2\", \"3\", \"4\", \"5\"]",
      "rateRqmaps": [],
      "rateInclusions": [],
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
    "sippCode": null,
    "source": "CO",
    "rateType": "best",
    "lat": "NA",
    "lng": "NA",
    "latDropOff": "NA",
    "lngDropOff": "NA"
  },
  "companyName": "Ace",
  "companyCode": "AC",
  "environment": "Test"
}
```

### Response success (status 200)

```
[
    {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": "1",
        "air": "Yes",
        "trans": "Automatic",
        "passengers": "5",
        "bags": "2",
        "sippCode": "CCAR",
        "doors": "4",
        "img": "https://www.acerentacar.com/CarPics/Nissan Versa.png",
        "carModel": "Nissan Versa",
        "km_included": "Unlimited Miles",
        "payment_option": "1",
        "netCommission": "25",
        "currency": "USD",
        "realBase": "263.69",
        "realTax": 144.54000000000002,
        "rateAmount": "408.23",
        "taxNotIncluded": 0,
        "ccrc": "QUM=",
        "isLocal": 0
    },
    {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": "1",
        "air": "Yes",
        "trans": "Automatic",
        "passengers": "4",
        "bags": "2",
        "sippCode": "ECAR",
        "doors": "4",
        "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
        "carModel": "Toyota Yaris",
        "km_included": "Unlimited Miles",
        "payment_option": "1",
        "netCommission": "25",
        "currency": "USD",
        "realBase": "266.63",
        "realTax": 145.20999999999998,
        "rateAmount": "411.84",
        "taxNotIncluded": 0,
        "ccrc": "QUM=",
        "isLocal": 0
    },
    ...
    {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": "3",
        "air": "Yes",
        "trans": "Automatic",
        "passengers": "5",
        "bags": "2",
        "sippCode": "CCAR",
        "doors": "4",
        "img": "https://www.acerentacar.com/CarPics/Nissan Versa.png",
        "carModel": "Nissan Versa",
        "km_included": "Unlimited Miles",
        "payment_option": "1",
        "netCommission": "25",
        "currency": "USD",
        "realBase": "736.40",
        "realTax": 254.05000000000007,
        "rateAmount": "990.45",
        "taxNotIncluded": 0,
        "ccrc": "QUM=",
        "isLocal": 0
    },
    {
        "companyName": "Ace",
        "companyCode": "AC",
        "rateType": "3",
        "air": "Yes",
        "trans": "Automatic",
        "passengers": "4",
        "bags": "2",
        "sippCode": "ECAR",
        "doors": "4",
        "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
        "carModel": "Toyota Yaris",
        "km_included": "Unlimited Miles",
        "payment_option": "1",
        "netCommission": "25",
        "currency": "USD",
        "realBase": "736.40",
        "realTax": 254.05000000000007,
        "rateAmount": "990.45",
        "taxNotIncluded": 0,
        "ccrc": "QUM=",
        "isLocal": 0
    },
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

## Get Selection

Returns all the available rates of the company with the SIPP code according to the parameters sent

### URL

- http://localhost/example-rental-company-implementation/web/companies/get-selection

### Request Parameters

Method POST

```
{
  "rates": [
    {
      "id": "1",
      "qualifier": "RC",
      "code": "RGZNET",
      "company_id": "34",
      "rate_type_id": "1",
      "payment_option": "1",
      "destination_coverage": null,
      "airport_id": "9999",
      "country_id": "254",
      "status": "1",
      "rate_term_id": "1",
      "entry_rate_type_id": "12",
      "commission_type_id": "2",
      "commission": "25",
      "franchise_id": null,
      "currency": null,
      "location_list": null,
      "source_coverage": "1",
      "dynamic_rate": null,
      "dummy_iata": "",
      "type_rental_cover": null,
      "sites": "[\"all\"]",
      "rateName": "Just Car",
      "companyCode": "AC",
      "companyName": "ACE",
      "companyCid": "",
      "companyIata": "12345678",
      "orders": "0",
      "inclusions": "[1, 2]",
      "rateRqmaps": [],
      "rateInclusions": [],
      "rateType": "just_car",
      "discountCodes": []
    },
    {
      "id": "2",
      "qualifier": "RC",
      "code": "RGZNET",
      "company_id": "34",
      "rate_type_id": "3",
      "payment_option": "1",
      "destination_coverage": null,
      "airport_id": "9999",
      "country_id": "254",
      "status": "1",
      "rate_term_id": "1",
      "entry_rate_type_id": "12",
      "commission_type_id": "2",
      "commission": "25",
      "franchise_id": null,
      "currency": null,
      "location_list": null,
      "source_coverage": "1",
      "dynamic_rate": null,
      "dummy_iata": "",
      "type_rental_cover": null,
      "sites": "[\"all\"]",
      "rateName": "Full Protection",
      "companyCode": "AC",
      "companyName": "ACE",
      "companyCid": "",
      "companyIata": "12345678",
      "orders": "2",
      "inclusions": "[\"1\", \"2\", \"3\", \"4\", \"5\"]",
      "rateRqmaps": [],
      "rateInclusions": [],
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
    "sippCode": "ECAR",
    "companyCode": "AC",
    "ccrc": "QUM="
  },
  "companyName": "Ace",
  "companyCode": "AC",
  "environment": "Test"
}
```

### Response success (status 200)

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
        "doors": "2-4",
        "passengers": "4",
        "bags": "2",
        "trans": "Automatic",
        "air": "Yes",
        "payment_option": "1",
        "km_included": "Mile",
        "currency": "USD",
        "realBase": "266.63",
        "realTax": 145.20999999999998,
        "rateAmount": "411.84",
        "taxNotIncluded": 0,
        "carInfo": {
            "ECAR": {
                "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
                "carModel": "Toyota Yaris",
                "sippCode": "ECAR",
                "km_included": "Mile",
                "companyName": "Ace",
                "doors": "2-4",
                "passengers": "4",
                "bags": "2",
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
        "doors": "2-4",
        "passengers": "4",
        "bags": "2",
        "trans": "Automatic",
        "air": "Yes",
        "payment_option": "1",
        "km_included": "Mile",
        "currency": "USD",
        "realBase": "736.40",
        "realTax": 254.05000000000007,
        "rateAmount": "990.45",
        "taxNotIncluded": 0,
        "carInfo": {
            "ECAR": {
                "img": "https://www.acerentacar.com/CarPics/Toyota Yaris.png",
                "carModel": "Toyota Yaris",
                "sippCode": "ECAR",
                "km_included": "Mile",
                "companyName": "Ace",
                "doors": "2-4",
                "passengers": "4",
                "bags": "2",
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
        "value": "2-4"
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
  "environment": "Production"
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