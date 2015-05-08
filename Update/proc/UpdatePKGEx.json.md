# example of update package
```XML
{
    "TPS_Errno": "DBUD-001", #Update Name (must be unique)  
    "type" : "database", # type (database, XML, file)  
    "execute" : "SQL", # operation type SQL or file  
    "SQL_QRY" : {  
        "TEST": "SHOW COLUMNS FROM `permissions` LIKE 'Library_%';", #test statement for SQL  
        "RESULT" : {    #expected results to compare note column name should match assoc  
            "Field" : [  
                "Library_View",
                "Library_Edit",
                "Library_Create"
            ],
            "Null" : [
                "YES",
                "YES",
                "YES"
            ]
        },
        "UPDATE_TYPE" : "FILE", #update source FILE or STATEMENT  
        "UPDATE" : "LIBRARY_UPDATE_1.sql" # update file or statement depending on Type
    }
}
```
