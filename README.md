Lab ApachePHP + NGINX + MySQL

Provisionamento automatizado de ambiente web contendo **NGINX (Load Balancer), ApachePHP e MySQL**, com deploy via Git e configuração dinâmica de banco de dados.

---

📌 Visão Geral

Este projeto utiliza JPS (Jelastic Packaging Standard) para criar automaticamente um ambiente completo composto por:

* Balanceador NGINX
* Servidor de aplicação Apache com PHP
* Banco de dados MySQL interno
* Deploy automatizado de aplicação via repositório Git
* Geração automática de arquivo de configuração do banco

O objetivo é fornecer um laboratório funcional e reproduzível para testes, demonstrações e validações técnicas.

---

🏗 Arquitetura

- O fluxo de funcionamento do ambiente ocorre da seguinte forma:
- O usuário acessa o ambiente via Internet, utilizando HTTP (ou HTTPS, se configurado).
- A requisição é recebida pelo NGINX Load Balancer, que atua como ponto único de entrada do ambiente.
- O NGINX encaminha a requisição internamente para o servidor ApachePHP, responsável por processar a aplicação.
- Quando necessário, a aplicação estabelece conexão com o MySQL interno, utilizando a rede privada do ambiente através da porta 3306.
- O banco de dados não é exposto publicamente, garantindo que apenas o servidor de aplicação possa acessá-lo.

📦 Componentes Provisionados

Durante a instalação, o JPS realiza:

* 1x NGINX (Load Balancer)
* 1x ApachePHP (Aplicação)
* 1x MySQL (Banco de Dados)

Além disso:

* Gera senha automática para o usuário `root`
* Clona repositório Git informado
* Cria arquivo `db_config.php` com credenciais dinâmicas

---

🔄 Fluxo de Provisionamento

1. Criação dos nós (NGINX, ApachePHP, MySQL)

2. Definição automática de senha do MySQL

3. Clone do repositório:

   ```
   https://github.com/joseph-lins/Apache-PHP_NGINX_DBMySQL.git
   ```

4. Criação do arquivo:

   ```
   /var/www/webroot/ROOT/db_config.php
   ```

5. Ambiente pronto para acesso via endpoint do NGINX

---

⚙️ Parâmetros Configuráveis

Git

| Parâmetro      | Descrição                       |
| -------------- | ------------------------------- |
| Git repo URL   | URL do repositório da aplicação |
| Git branch/tag | Branch ou tag a ser clonada     |

ApachePHP

Baseado na imagem:

```
jelastic/apachephp:2.4.54-php-7.4.33
```

* Apache 2.4.54
* PHP 7.4

> A versão depende das tags disponíveis no registry.

MySQL

Imagem utilizada:

```
jelastic/mysql:9.3.0-almalinux-9
```

---

📂 Estrutura do Deploy

A aplicação é clonada em:

```
/var/www/webroot/ROOT
```

Arquivo gerado automaticamente:

```php
<?php
define('DB_HOST', 'sqldb');
define('DB_PORT', '3306');
define('DB_NAME', 'demo_access');
define('DB_USER', 'root');
define('DB_PASS', 'SENHA_GERADA');
```

---

🌐 Como Acessar

1. Após a instalação, acesse o endpoint público do NGINX.
2. O NGINX encaminha as requisições para o ApachePHP.
3. A aplicação conecta-se ao MySQL via host interno `sqldb`.

---

🔐 Segurança

* O MySQL não é exposto publicamente (`JELASTIC_EXPOSE: false`)
* Comunicação entre aplicação e banco ocorre apenas via rede interna
* Senha do banco gerada automaticamente
* Ponto único de entrada via NGINX

---

🛠 Troubleshooting Básico

Verificar aplicação

No node ApachePHP:

```bash
ls -la /var/www/webroot/ROOT
```

Verificar conexão MySQL

```bash
mysql -h sqldb -u root -p
```

Verificar logs Apache

```bash
tail -f /var/log/httpd/error_log
```

Verificar logs NGINX

```bash
tail -f /var/log/nginx/error.log
```

---

🎯 Objetivo do Lab

Este laboratório é indicado para:

* Demonstrações técnicas
* Testes de deploy automatizado
* Treinamentos internos
* Validação de integração Apache + MySQL
* Simulações de arquitetura com Load Balancer

<img width="967" height="660" alt="image" src="https://github.com/user-attachments/assets/5788a9ed-c9a0-4506-a6d2-3731c1a67964" />
