# ChatLogger
![](http://isitmaintained.com/badge/resolution/kenygamer/ChatLogger.svg)
![](https://img.shields.io/github/release/kenygamer/ChatLogger/all.svg)
![](https://img.shields.io/github/downloads/kenygamer/ChatLogger/total.svg)

ChatLogger is a PocketMine-MP plugin to log your server chat, keeping the performance in mind. It features a /export command to dump a player chat log at a given date.
## Commands
| Command | Usage | Description |
| ------- | ----- | ----------- |
| `/export` | `/export <player> <date: mm-dd-yy>` | Dumps a player chat log at a given date. |
## Permissions
```yml
chatlogger:
 description: Allows access to all ChatLogger features.
 default: false
 children:
  chatlogger.bypass:
   description: Allows to bypass chat logging.
   default: false
  chatlogger.command:
   description: Allows access to all ChatLogger commands.
   default: false
   children:
    chatlogger.command.export:
     description: Allows access to the ChatLogger export command.
     default: op
```
