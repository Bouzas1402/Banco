USE bancodb;

--

CREATE TABLE cliente (
                         dni VARCHAR (9) PRIMARY KEY,
                         nombre VARCHAR (20),
                         apellido1 VARCHAR (20),
                         apellido2 VARCHAR (20),
                         necionalidad VARCHAR (20),
                         telefono VARCHAR (20),
                         contrasena VARCHAR(500)
);

--

CREATE TABLE cuenta (
                        iban VARCHAR (25) PRIMARY KEY,
                        saldo NUMERIC (8,2),
                        fecha_de_apertura DATETIME DEFAULT CURRENT_TIMESTAMP,
                        dni VARCHAR(9),
                        CONSTRAINT cuenta_cliente_fk FOREIGN KEY (dni) REFERENCES cliente (dni)
);



--

CREATE TABLE movimientos_cuenta (
                                    iban VARCHAR (25),
                                    fecha_movimiento  DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    cantidad DECIMAL (8,2),
                                    cuenta_recepcion VARCHAR (25),
                                    PRIMARY KEY (IBAN, fecha_movimiento),
                                    CONSTRAINT movimientos_cuenta_fk FOREIGN KEY (iban) REFERENCES cuenta (iban)
);


