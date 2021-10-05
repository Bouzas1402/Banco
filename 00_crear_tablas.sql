USE banco;

--

CREATE TABLE cliente (
                         DNI VARCHAR (9) PRIMARY KEY,
                         nombre VARCHAR (20),
                         apellido1 VARCHAR (20),
                         apellido2 VARCHAR (20),
                         nacionalidad VARCHAR (20),
                         telefono VARCHAR (9),
                         fecha_nacimiento DATE
);

--

CREATE TABLE cuenta (
                        id_cuenta INT PRIMARY KEY,
                        IBAN VARCHAR (25) NOT NULL UNIQUE,
                        contrasena VARCHAR(50),
                        saldo NUMERIC (8,2),
                        fecha_de_apertura DATETIME DEFAULT CURRENT_TIMESTAMP,
                        DNI VARCHAR(9),
                        CONSTRAINT cuenta_cliente_fk FOREIGN KEY (DNI) REFERENCES cliente (DNI)
);

--

CREATE TABLE movimientos_cuenta (
                                    id_cuenta INT,
                                    fecha_movimiento  DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    cantidad DECIMAL (8,2),
                                    i_s INT,
                                    PRIMARY KEY (id_cuenta, fecha_movimiento),
                                    CONSTRAINT movimientos_cuenta_fk FOREIGN KEY (id_cuenta) REFERENCES cuenta (id_cuenta)
);
