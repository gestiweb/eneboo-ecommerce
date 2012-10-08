DROP TABLE IF EXISTS pedidoscli_seq;
CREATE TABLE pedidoscli_seq (
  id int(10) NOT NULL auto_increment,
  texto varchar(10) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS lineaspedidoscli_seq;
CREATE TABLE lineaspedidoscli_seq (
  id int(10) NOT NULL auto_increment,
  texto varchar(10) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS clientes_seq;
CREATE TABLE clientes_seq (
  id int(10) NOT NULL auto_increment,
  texto varchar(10) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS dirclientes_seq;
CREATE TABLE dirclientes_seq (
  id int(10) NOT NULL auto_increment,
  texto varchar(10) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;