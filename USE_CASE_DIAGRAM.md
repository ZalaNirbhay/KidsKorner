# Use Case Diagram - KidsKorner

This diagram illustrates the interactions between actors (User, Admin) and the system's use cases.

```mermaid
usecaseDiagram
    actor "Customer" as User
    actor "Administrator" as Admin

    package "KidsKorner System" {
        usecase "Register/Login" as UC1
        usecase "Browse Products" as UC2
        usecase "Search Products" as UC3
        usecase "Manage Cart" as UC4
        usecase "Place Order" as UC5
        usecase "Make Payment" as UC6
        usecase "View Order History" as UC7
        usecase "Write Review" as UC8
        usecase "Manage Profile" as UC9
        
        usecase "Manage Products" as UC10
        usecase "Manage Categories" as UC11
        usecase "Manage Orders" as UC12
        usecase "Manage Users" as UC13
        usecase "Manage Reviews" as UC14
        usecase "View Dashboard" as UC15
    }

    User --> UC1
    User --> UC2
    User --> UC3
    User --> UC4
    User --> UC5
    User --> UC6
    User --> UC7
    User --> UC8
    User --> UC9

    Admin --> UC1
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14
    Admin --> UC15

    UC5 ..> UC6 : <<include>>
    UC7 ..> UC8 : <<extend>>
```
