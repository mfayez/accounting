DROP TABLE IF EXISTS `Address`;
DROP TABLE IF EXISTS `Delivery`;
DROP TABLE IF EXISTS `Discount`;
DROP TABLE IF EXISTS `Invoice`;
DROP TABLE IF EXISTS `InvoiceLine`;
DROP TABLE IF EXISTS `Issuer`;
DROP TABLE IF EXISTS `Payment`;
DROP TABLE IF EXISTS `Receiver`;
DROP TABLE IF EXISTS `TaxTotal`;
DROP TABLE IF EXISTS `TaxableItem`;
DROP TABLE IF EXISTS `Value`;
DROP TABLE IF EXISTS `Item`;
DROP TABLE IF EXISTS ETAItems;

create table Address (
    Id int AUTO_INCREMENT NOT NULL,
    branchID varchar(50) NULL,
    country varchar(50) NULL,
    governate varchar(50) NULL,
    regionCity varchar(50) NULL,
    street varchar(50) NULL,
    buildingNumber varchar(50) NULL,
    postalCode varchar(50) NULL,
    floor varchar(50) NULL,
    room varchar(50) NULL,
    landmark varchar(50) NULL,
    additionalInformation varchar(50) NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_Address PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;
create table Issuer (
    Id int AUTO_INCREMENT NOT NULL,
    address_id int,
    type varchar(50) NULL,
    issuer_id varchar(50) NULL,
    name varchar(50) NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_Issuer PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;

create table Receiver (
    Id int AUTO_INCREMENT NOT NULL,
    address_id int,
    type varchar(50) NULL,
    receiver_id varchar(50) NULL,
    name varchar(50) NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_Receiver PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;

create table Payment (
    Id int AUTO_INCREMENT NOT NULL,
    bankName varchar(50) NULL,
    bankAddress varchar(50) NULL,
    bankAccountNo varchar(50) NULL,
    bankAccountIBAN varchar(50) NULL,
    swiftCode varchar(50) NULL,
    terms varchar(50) NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_Payment PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;
create table Delivery (
    Id int AUTO_INCREMENT NOT NULL,
    approach varchar(50) NULL,
    packaging varchar(50) NULL,
    dateValidity datetime,
    exportPort varchar(50) NULL,
    grossWeight decimal(9,2) NOT NULL,
    netWeight decimal(9,2) NOT NULL,
    terms varchar(50) NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_Delivery PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;
create table Value (
    Id int AUTO_INCREMENT NOT NULL,	
    currencySold varchar(5) NULL,
    amountEGP decimal(9,3),
	amountSold decimal(9,3) NULL,
	currencyExchangeRate decimal(9,3) NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_UnitValue PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;
create table Discount (
    Id int AUTO_INCREMENT NOT NULL,
    rate decimal(9,3) NOT NULL,
    amount decimal(9.3) NOT NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_Discount PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;
create table InvoiceLine (
    Id int AUTO_INCREMENT NOT NULL,
    description varchar(50) NULL,
    itemType varchar(50) NULL,
    itemCode varchar(50) NULL,
    unitType varchar(50) NULL,
    quantity int NOT NULL,
    internalCode varchar(50) NULL,
    salesTotal decimal(9,3),
    total decimal(9,3),
    valueDifference decimal(9,3) NOT NULL,
    totalTaxableFees decimal(9,3) NOT NULL,
    netTotal decimal(9,3),
    itemsDiscount decimal(9,3) NOT NULL,
    unitValue_id int,
    discount_id int,
	invoice_id int not null,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_InvoiceLine PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;

create table TaxableItem (
    Id int AUTO_INCREMENT NOT NULL,
    taxType varchar(50) NULL,
    amount decimal(9,3) NOT NULL,
    subType varchar(50) NULL,
    rate decimal(9,3) NOT NULL,
	invoiceline_id int NOT NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_TaxableItem PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;
create table TaxTotal (
    Id int AUTO_INCREMENT NOT NULL,
    taxType varchar(50) NULL,
    amount decimal(9,3) NOT NULL,
	invoice_id int not null,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_TaxTotal PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;
;
create table Invoice (
    Id int AUTO_INCREMENT NOT NULL,
    issuer_id int,
    receiver_id int,
    documentType varchar(50) NULL,
    documentTypeVersion varchar(50) NULL,
    dateTimeIssued datetime,
    taxpayerActivityCode varchar(50) NULL,
    internalID varchar(50) NULL,
    purchaseOrderReference varchar(50) NULL,
    purchaseOrderDescription varchar(50) NULL,
    salesOrderReference varchar(50) NULL,
    salesOrderDescription varchar(50) NULL,
    proformaInvoiceNumber varchar(50) NULL,
    payment_id int,
    delivery_id int,
    totalDiscountAmount decimal(9,3) NOT NULL,
    totalSalesAmount decimal(9,3) NOT NULL,
    netAmount decimal(9,3) NOT NULL,
    totalAmount decimal(9,3) NOT NULL,
    extraDiscountAmount decimal(9,3) NOT NULL,
    totalItemsDiscountAmount decimal(9,3) NOT NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_Invoice PRIMARY KEY CLUSTERED
   (
      Id asc
   )
)
;
create table Item (
    Id int AUTO_INCREMENT NOT NULL,
    codeType varchar(50) NULL,
    parentCode varchar(50) NULL,
    itemCode varchar(50) NOT NULL,
    codeName varchar(50) NULL,
    codeNameAr varchar(50) NULL,
    activeFrom datetime,
    activeTo datetime,
    description varchar(50) NULL,
    descriptionAr varchar(50) NULL,
    requestReason varchar(50) NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
CONSTRAINT PK_Item PRIMARY KEY CLUSTERED
   (
      Id asc
   ),
	CONSTRAINT CODE_ITEM_IDX UNIQUE INDEX (itemCode)
)
;
CREATE TABLE ETAItems(
  Id int AUTO_INCREMENT NOT NULL PRIMARY KEY
  ,codeUsageRequestID               INTEGER  NOT NULL 
  ,codeTypeName                     VARCHAR(3) NOT NULL
  ,codeID                           INTEGER  NOT NULL
  ,itemCode                         VARCHAR(20) NOT NULL
  ,codeNamePrimaryLang              VARCHAR(24) NOT NULL
  ,codeNameSecondaryLang            VARCHAR(24) NOT NULL
  ,descriptionPrimaryLang           VARCHAR(24) NOT NULL
  ,descriptionSecondaryLang         VARCHAR(24) NOT NULL
  ,parentCodeID                     INTEGER  NOT NULL
  ,parentItemCode                   INTEGER  NOT NULL
  ,parentCodeNamePrimaryLang        VARCHAR(32) NOT NULL
  ,parentCodeNameSecondaryLang      VARCHAR(30) NOT NULL
  ,parentLevelName                  VARCHAR(25) NOT NULL
  ,levelName                        VARCHAR(17) NOT NULL
  ,requestCreationDateTimeUtc       VARCHAR(28) NOT NULL
  ,codeCreationDateTimeUtc          VARCHAR(28) NOT NULL
  ,activeFrom                       VARCHAR(20) NOT NULL
  ,activeTo                         VARCHAR(30)
  ,active                           VARCHAR(4) NOT NULL
  ,status                           VARCHAR(8) NOT NULL
  ,statusReason                     VARCHAR(30)
  ,ownerTaxpayerrin                 INTEGER  NOT NULL
  ,ownerTaxpayername                VARCHAR(30) NOT NULL
  ,ownerTaxpayernameAr              VARCHAR(30) NOT NULL
  ,requesterTaxpayerrin             INTEGER  NOT NULL
  ,requesterTaxpayername            VARCHAR(30) NOT NULL
  ,requesterTaxpayernameAr          VARCHAR(30) NOT NULL
  ,codeCategorizationlevel1id       INTEGER  NOT NULL
  ,codeCategorizationlevel1name     VARCHAR(21) NOT NULL
  ,codeCategorizationlevel1nameAr   VARCHAR(23) NOT NULL
  ,codeCategorizationlevel2id       INTEGER  NOT NULL
  ,codeCategorizationlevel2name     VARCHAR(26) NOT NULL
  ,codeCategorizationlevel2nameAr   VARCHAR(14) NOT NULL
  ,codeCategorizationlevel3id       INTEGER  NOT NULL
  ,codeCategorizationlevel3name     VARCHAR(51) NOT NULL
  ,codeCategorizationlevel3nameAr   VARCHAR(40) NOT NULL
  ,codeCategorizationlevel4id       INTEGER  NOT NULL
  ,codeCategorizationlevel4name     VARCHAR(32) NOT NULL
  ,codeCategorizationlevel4nameAr   VARCHAR(30) NOT NULL
)
;
ALTER TABLE Invoice ADD CONSTRAINT fk_invoice_issuer_id FOREIGN KEY (issuer_id) REFERENCES Issuer(id);
ALTER TABLE Invoice ADD CONSTRAINT fk_invoice_receiver_id FOREIGN KEY (receiver_id) REFERENCES Receiver(id);
ALTER TABLE Invoice ADD CONSTRAINT fk_invoice_payment_id FOREIGN KEY (payment_id) REFERENCES Payment(id);
ALTER TABLE Invoice ADD CONSTRAINT fk_invoice_delivery_id FOREIGN KEY (delivery_id) REFERENCES Delivery(id);
ALTER TABLE Issuer ADD CONSTRAINT fk_issuer_address_id FOREIGN KEY (address_id) REFERENCES Address(id);
ALTER TABLE TaxTotal ADD CONSTRAINT fk_TaxTotal_invoice_id FOREIGN KEY (invoice_id) REFERENCES Invoice(id);
ALTER TABLE TaxableItem ADD CONSTRAINT fk_TaxableItem_invoiceline_id FOREIGN KEY (invoiceline_id) REFERENCES InvoiceLine(id);
ALTER TABLE InvoiceLine ADD CONSTRAINT fk_invoiceline_unitvalue_id FOREIGN KEY (unitValue_id) REFERENCES Value(id);
ALTER TABLE InvoiceLine ADD CONSTRAINT fk_invoiceline_discount_it FOREIGN KEY (discount_id) REFERENCES Discount(id);
ALTER TABLE InvoiceLine ADD CONSTRAINT fk_invoiceline_invoice_id FOREIGN KEY (invoice_id) REFERENCES Invoice(id);
ALTER TABLE Receiver ADD CONSTRAINT fk_receiver_address_id FOREIGN KEY (address_id) REFERENCES Address(id);

