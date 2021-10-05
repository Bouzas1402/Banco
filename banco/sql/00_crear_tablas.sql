USE banco;

--

CREATE TABLE cliente (
                         DNI VARCHAR (9) PRIMARY KEY,
                         nombre VARCHAR (20),
                         apellido1 VARCHAR (20),
                         apellido2 VARCHAR (20),
                         necionalidad VARCHAR (20),
                         telefono VARCHAR (20),
                         contrasena VARCHAR(10)
);

INSERT INTO cliente VALUES ("3456543D", "Enrique", "quevedo");
INSERT INTO cliente VALUES ("9876443S", "Carlos", "quevedo");
INSERT INTO cliente VALUES ("6254323T", "Luis", "quevedo");
INSERT INTO cliente VALUES ("2223413Z", "Juan", "quevedo");
INSERT INTO cliente VALUES ("5363542T", "Ana", "quevedo");

--

CREATE TABLE cuenta (
                        IBAN VARCHAR (25) PRIMARY KEY,
                        saldo NUMERIC (8,2),
                        fecha_de_apertura DATETIME DEFAULT CURRENT_TIMESTAMP,
                        DNI VARCHAR(9),
                        CONSTRAINT cuenta_cliente_fk FOREIGN KEY (DNI) REFERENCES cliente (DNI)
);

INSERT INTO cuenta (IBAN, saldo, DNI) VALUES ("IBAN45633673457546", 1900.50, "3456543D");
INSERT INTO cuenta (IBAN, saldo, DNI) VALUES ("IBAN57687544513452", 8120.79, "3456543D");
INSERT INTO cuenta (IBAN, saldo, DNI) VALUES ("IBAN78967563423443", 3267.99, "3456543D");
INSERT INTO cuenta (IBAN, saldo, DNI) VALUES ("IBAN13423590086532", 1245.50, "9876443S");
INSERT INTO cuenta (IBAN, saldo, DNI) VALUES ("IBAN34778545256673", 1768.34, "9876443S");
INSERT INTO cuenta (IBAN, saldo, DNI) VALUES ("IBAN88812245234526", 6245.20, "6254323T");
INSERT INTO cuenta (IBAN, saldo, DNI) VALUES ("IBAN24434577221163", 4695.15, "2223413Z");
INSERT INTO cuenta (IBAN, saldo, DNI) VALUES ("IBAN86453452211111", 3760.82, "5363542T");

--

CREATE TABLE movimientos_cuenta (
                                    IBAN VARCHAR (25),
                                    fecha_movimiento  DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    cantidad DECIMAL (8,2),
                                    cuenta_recepcion VARCHAR (25),
                                    PRIMARY KEY (IBAN, fecha_movimiento),
                                    CONSTRAINT movimientos_cuenta_fk FOREIGN KEY (IBAN) REFERENCES cuenta (IBAN)
);


