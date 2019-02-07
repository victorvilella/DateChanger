## DataChanger
Esta classe é resultado de um processo seletivo da DBSeller. Não deve ser usada em ambientes de produção.

##Instalação
`composer install`

## Como usar
`php index.php data operador valor`

Sendo:
* `data` no formato `d/m/Y H:i`
* `operador` como as operaões de soma `+` e subtração `-` 
* `valor` como um inteiro (em minutos) que se deseja adicionar ou subtrair da data informada

## Testes
`phpunit`