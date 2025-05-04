# YoPrint

Please run the following commands
- composer i
- php artisan migrate
- npm i
- npm run dev


Contains 3 Models
- Product
- CsvImportBatch
    - Keep Track of Import Batches for futher reference or rollback purposes
- ProductImportDetail
    - Pivot table to link CsvImportBatch to Product

 

  
