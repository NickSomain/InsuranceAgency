drop database insuranceagency;
create database insuranceagency;
use insuranceagency;

create table Clients (
	client_id INT NOT NULL AUTO_INCREMENT,
	client_name VARCHAR(45) NOT NULL,
	client_surname VARCHAR(45) NOT NULL,
	client_phone VARCHAR(20) NOT NULL,
	client_email VARCHAR(45) NOT NULL,
	PRIMARY KEY (client_id)
);

create table Insurance_list (
	insurance_id INT NOT NULL AUTO_INCREMENT,
    insurance_type VARCHAR(100) NOT NULL,
    insurance_price INT NOT NULL,
    insurance_description VARCHAR(2000) NOT NULL,
    PRIMARY KEY(insurance_id)
);

create table Contracts (
	contract_id INT NOT NULL AUTO_INCREMENT,
    client_id INT NOT NULL,
    insurance_id INT NOT NULL,
    PRIMARY KEY(contract_id),
    CONSTRAINT fk_Contracts_Clients FOREIGN KEY (client_id) REFERENCES InsuranceAgency.Clients (client_id),
    CONSTRAINT fk_Contracts_Insurance_list FOREIGN KEY (insurance_id) REFERENCES InsuranceAgency.Insurance_list (insurance_id)
);

INSERT INTO Clients (client_id, client_name, client_surname, client_phone, client_email)
	VALUES
		 (1, "Nikita", "Somov", "89661891203", "n1546875@gmail.com"),
         (2, "Jay", "Miller", "11793452162", "jaymiller1@gmail.com"),
         (3, "Don", "Robertson", "34516784112", "n1546875@gmail.com");
         
INSERT INTO Insurance_list (insurance_id, insurance_type, insurance_price, insurance_description)
	VALUES
		(1, "auto insurance", 50000, "Car insurance in case of an accident or breakdown"),
        (2, "health insurance", 100000, "Insurance in case of illness when treatment is required"),
        (3, "house insurance", 300000, "Insurance against emergencies such as fire, intruders or natural disasters");
    
INSERT INTO Contracts (contract_id, client_id, insurance_id)
	VALUES
		(1, 1, 1),
        (2, 1, 3),
        (3, 2, 1),
        (4, 3, 2),
        (5, 3, 3);