# Frameshort
---
Credit: Simon Malpel (simon.malpel@orange.fr)
## How to do?

### - Create view:
- First, you can edit the routes.yaml configuration file. (**src/config/routes.yaml**)
```yaml
    - host: www.my-wonderful-website.com
      type: GET|POST
      pattern: "/hello-world"
      controller: MyController::MyCallableFunction
      subsite: null
```

- Second you can create controller or use "Welcome"
- Thirdly you need to create a view in **src/views/My-Wonderful-View.php**
- For ended, call this view in controller with "MyCallableFunction" function 