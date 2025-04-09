# 📬 RegisterPage

Formulário de cadastro com envio automático por e-mail.  
Projeto construído em PHP utilizando **PHPMailer**, **Dotenv** e **Docker**.  
Deploy automatizado na **Render**, e front-end estático opcionalmente hospedado na **Vercel**.

---

## 🧱 Estrutura do Projeto

```
RegisterPage/
├── index.html               # Página HTML do formulário
├── style.css                # Estilos do formulário
├── form-service/
│   ├── dockerfile           # Dockerfile com Apache + PHP + Composer
│   ├── public/
│   │   └── enviarFormulario.php  # Processa e envia o formulário
│   └── .env                 # (usado localmente - não versionado)
├── composer.json            # Dependências do PHP
├── render.yaml              # Configuração para deploy na Render
└── README.md                # Este arquivo 😎
```

---

## ⚙️ Tecnologias Usadas

- **PHP 8.2**
- **PHPMailer** – Envio de e-mails via SMTP
- **vlucas/phpdotenv** – Gerenciador de variáveis de ambiente
- **Docker** – Ambiente isolado para o back-end
- **Render** – Hospedagem automatizada via imagem Docker
- **Vercel (opcional)** – Hospedagem do front-end estático

---

## 📄 Exemplo de `.env` (local)

```env
SMTP_HOST=smtp.exemplo.com
SMTP_PORT=465
SMTP_USER=usuario
SMTP_PASS=senha
```

> **Importante:** Em produção, essas variáveis são configuradas diretamente no painel do Render.

---

## 🐳 Dockerfile

Local: `form-service/dockerfile`

```Dockerfile
# Usa imagem do PHP com o Apache
FROM php:8.2-apache

# Instala extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath

# Copia o Composer da imagem oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho dentro do container
WORKDIR /var/www/html

# Copia todos os arquivos do projeto para o container
COPY . .

# Configura o Apache: define a pasta "public" como DocumentRoot
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Instala as dependências do Composer (usando o composer.json presente na raiz ou na pasta correta)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ajusta permissões para que o Apache tenha acesso aos arquivos e ativa o mod_rewrite
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Expõe a porta padrão 80
EXPOSE 80

```

---

## 🚀 Deploy no Render

### `render.yaml`

```yaml
services:
  - type: web
    name: registerpage-php
    env: docker
    dockerfilePath: form-service/dockerfile
    rootDirectory: .
    plan: free
    autoDeploy: true
    envVars:
      - key: SMTP_HOST
        value: smtp.smtp2go.com
      - key: SMTP_PORT
        value: 465
      - key: SMTP_USER
        value: mr1c10
      - key: SMTP_PASS
        value: SUA_SENHA_SECRETA
```

---

## 🌐 Conectando o Front-end

No `index.html`, defina a ação do formulário para o domínio do Render:

```html
<form action="https://registerpage-php.onrender.com/enviarFormulario.php" method="POST">
  <!-- Campos aqui -->
</form>
```

---

## 📨 Código do Back-end

Local: `form-service/public/enviarFormulario.php`

```php
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad(); // evita erro se .env estiver ausente

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->setFrom('seu@email.com', $_POST['nome']);
        $mail->addAddress('destino@email.com');

        $mail->isHTML(true);
        $mail->Subject = 'Nova inscrição';
        $mail->Body = "
            <h2>Nova inscrição:</h2>
            <p><strong>Nome:</strong> {$_POST['nome']}</p>
            <p><strong>Gênero:</strong> {$_POST['genero']}</p>
            <p><strong>Idade:</strong> {$_POST['idade']}</p>
            <p><strong>Telefone:</strong> {$_POST['telefone']}</p>
            <p><strong>Linguagens:</strong> {$_POST['linguagens']}</p>
            <p><strong>Motivo:</strong><br>{$_POST['porque']}</p>
        ";

        $mail->send();
        echo "Email enviado!";
    } catch (Exception $e) {
        echo "Erro: {$mail->ErrorInfo}";
    }
} else {
    echo "Requisição inválida.";
}
```

---

## 🔍 Rodando localmente com Docker

```bash
# Clone o projeto
git clone https://github.com/seu-usuario/RegisterPage.git
cd RegisterPage/form-service

# Build da imagem
docker build -t registerpage .

# Iniciar container
docker run -d -p 8080:80 registerpage

# Acesse em: http://localhost:8080
```

---

## 📌 Observações

- O front-end (`index.html`, `style.css`) pode ser hospedado separadamente.
- Use `safeLoad()` no PHP para não depender de `.env` no ambiente de produção.
- O Composer deve instalar as dependências com `composer install` durante o build da imagem.

---

## 📫 Contato

Desenvolvido por **Mauricio Rodrigues**  
[GitHub @MR1C10](https://github.com/MR1C10)  
Projeto com fins educativos e de portfólio.

---