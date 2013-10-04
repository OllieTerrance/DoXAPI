Introduction
============

DoXAPI is an online interface for communicating with DoXCloud.


API endpoints
=============

The API only provides two endpoints:

* **auth.php** - login or register a user
* **sync.php** - download a user's tasks, optionally including a list to sync

To login, make an auth call with `email`, `pass` and `submit` parameters (where `submit` is one of either `"login"` or `"register"`).  To logout, just send `submit` as `"logout"`.

To get a list of tasks, make a GET request to the sync endpoint using the cookies received from the auth call (including user details is not required).  If you have tasks to include, use a POST request with `tasks` or `done` set to a list of task strings.


Database structure
==================

The server behind the API uses three tables for storage.

```sql
CREATE TABLE IF NOT EXISTS `done` (
  `did` int(10) NOT NULL AUTO_INCREMENT,
  `sid` varchar(5) NOT NULL,
  `uid` int(10) NOT NULL,
  `task` varchar(4096) NOT NULL,
  PRIMARY KEY (`did`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tasks` (
  `tid` int(10) NOT NULL AUTO_INCREMENT,
  `sid` varchar(5) NOT NULL,
  `uid` int(10) NOT NULL,
  `task` varchar(4096) NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(320) NOT NULL,
  `pass` varchar(32) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;
```
