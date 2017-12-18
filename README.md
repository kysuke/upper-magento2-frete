# upper-magento2-frete
# Configuration
* Default json file to Calculate the Shipping where the first node is the zipcode range and inside company Key=>Value is Qty > Value(Value/qty shipping)
* ```
  {
    "15500000~15505500": { 
      "Company 1": [
        {
          "1": 10,
          "2": 15,
          "3": 20,
          "4": 25,
          "5": 30
        }
      ],
      "Company 2": [
        {
          "1": 15,
          "2": 20,
          "3": 25,
          "4": 30,
          "5": 35
        }
      ],
      "Company 3": [
        {
          "1": 20,
          "2": 25,
          "3": 30,
          "4": 35,
          "5": 40
        }
      ]
    },
    "15600000~15600999": {
      "Company 1": [
        {
          "1": 12,
          "2": 17,
          "3": 22,
          "4": 27,
          "5": 32
        }
      ],
      "Company 2": [
        {
          "1": 17,
          "2": 22,
          "3": 27,
          "4": 32,
          "5": 37
        }
      ],
      "Company 3": [
        {
          "1": 22,
          "2": 27,
          "3": 32,
          "4": 37,
          "5": 42
        }
      ]
    }
  }
```
