# ChatLogger

ChatLogger is a PocketMine-MP plugin to log your server chat. It features a /report command to dump a player chat log at a given date.
## Commands
| Command | Usage | Description |
| ------- | ----- | ----------- |
| `/report` | `/report <player> <date: mm-dd-yy>` | Dumps a player chat log at a given date. |
## Permissions
```yml
chatlogger:
 description: Allows access to all ChatLogger features.
 default: false
 children:
  chatlogger.command.report:
   description: Allows access to the report command.
   default: op
  chatlogger.bypass:
   description: Allows to bypass chat logging.
   default: false
```
