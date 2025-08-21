
Simple MariaDB backup (PowerShell)
Usage examples:
  powershell -ExecutionPolicy Bypass -File .\public\export-local-db.ps1 -DbName cake_shop
  powershell -ExecutionPolicy Bypass -File .\public\export-local-db.ps1 -DbName cake_shop -Timestamp
#>

## Auto backup
    - [X] backup ps1 file
    - [ ] write schedule file auto backup file every 5 min

- [X] Login
- [X] Register
- [X] Order
- [X] Add to cart
    - [X] It cant remember previous cake quantity, otherwise it reset
    - [X] How to make some feature let it redirect to home page or Login , Register for buy cake??
    - [X] How to implement change cake quantity the total price will automate change??
    - [ ] Does we have to save cart to database??
- [ ] Telegram Bot
- [ ] CMS
    - [X] Staff Login Page
    - [ ] Staff Register Page, (How to only let my employers can use this page?? for create the staff account with UI, not at db to insert xxxxx)
    - [ ] Manage Product materials
    - [X] Manage Order process or not
- [ ] Discord bot
- [X] Logout.php
- [ ] Login by name or eamil
- [ ] Can Choice add to cart or buy directly
- [X] Index Page choice 2 quantity but to shoppy cart only add one
- [ ] When i add some product, i dont want it redirect to my orders page
    If let it redirect to my orders page will effect UX,
so maybe let it keep at index page
choice some next product, when he want to see what he add to cart, he click manully

- [ ] customer service can communicate with customer via anoymous call
- [ ] when i add cake to cart want to animation

- [ ] Manage food safety period


- [ ] dockerize 
    - [ ]  ONE database for both apps
    - [ ] make sure compose.yaml file password safety
    - [ Not recommand ] volume local host : C:\xampp\mysql\data\cake_shop correspose to docker component
    - [ ]  No tables found in database. 
        - Find a solution import table


### admin_dashboard.php
- [ ] when admin click process this order, annote his third part app like discord or telegram or line