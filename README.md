Weather Symfony API 🎼
======
The main endpoint 'check' gets a name of a German city , then makes a call to Open Weather API to create a City instance. Finally it checks the criteria specified in '/src/Infrastructure/City/Resources/services.yaml' and returns a summary.
## Deploy
### PHP's built-in Web Server
Execute the following command to deploy the project locally in http://127.0.0.1:8000/check?city=Berlin
```bash
php bin/console server:run
```
## Tests
### Run units tests and functional test
```bash
php bin/phpunit
```
## Criteria
To modify the criteria, open the project file in '/src/Infrastructure/City/Resources/services.yaml' and do the following 
changes in the parameter 'criteria_to_check' for each case:
### Change order
Change the order of the array elements. Example:
```
parameters:
    criteria_to_check:
        - { alias: 'rival', active: true }
        - { alias: 'daytemp', active: true }
        - { alias: 'naming', active: true }
```
### Disable criteria
Update the active prop of the element to disable. Example:
```
parameters:
    criteria_to_check:
        - { alias: 'rival', active: false }
        - { alias: 'daytemp', active: true }
        - { alias: 'naming', active: true }
```
### Add new criteria
This requires to create a new AbstractChecker implementation and add a new condition in CheckerFactory to return the new checker. 
After this add a new element to the criteria parameter in services with the alias value of the new implementation. 
```
parameters:
    criteria_to_check:
        - { alias: 'rival', active: false }
        - { alias: 'daytemp', active: true }
        - { alias: 'naming', active: true }
        - { alias: 'new_criteria', active: true }
```