
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/498fa806a51647959cbeb451db0b7d3e)](https://app.codacy.com/manual/JgPhil/Snowtricks?utm_source=github.com&utm_medium=referral&utm_content=JgPhil/Snowtricks&utm_campaign=Badge_Grade_Dashboard)

<article class="markdown-body entry-content container-lg" itemprop="text">
<h1>Projet6 Openclassrooms- SnowTricks</h1>
    <p>Creation of a community site focused on the presentation of snowboard figures via the Symfony framework.</p>
    <h2>Environment used during development</h2>
    </a>Environment used during development</h2>
    <ul>
        <li>
            <p>Apache 2.4.41</p>
        </li>
        <li>
            <p>PHP 7.4.3</p>
        </li>
        <li>
            <p>MySQL 8.0.2</p>
        </li>
        <li>
            <p>Composer 1.10.1</p>
        </li>
        <li>
            <p>Git 2.25.1</p>
        </li>
        <li>
            <p>Symfony 5.1</p>
        </li>
        <li>
            <p>jQuery 3.4.1</p>
        </li>
        <li>
            <p>Bootstrap 4.4.1</p>
        </li>
    </ul>
    <h2>Installation</h2>
    <h3>Environment setup</h3>
    <p>It is necessary to have an Apache / Php / Mysql environment.<br>
        Depending on your operating system, several servers can be installed:</p>
    <ul>
        <li>
            <p>Windows : WAMP (<a href="http://www.wampserver.com/" rel="nofollow">http://www.wampserver.com/</a>)</p>
        </li>
        <li>
            <p>MAC : MAMP (<a href="https://www.mamp.info/en/mamp/" rel="nofollow">https://www.mamp.info/en/mamp/</a>)
            </p>
        </li>
        <li>
            <p>Linux : LAMP (<a href="https://doc.ubuntu-fr.org/lamp" rel="nofollow">https://doc.ubuntu-fr.org/lamp</a>)
            </p>
        </li>
        <li>
            <p>Cross system: XAMP (<a href="https://www.apachefriends.org/fr/index.html"
                    rel="nofollow">https://www.apachefriends.org/fr/index.html</a>)</p>
        </li>
    </ul>
    <p>Symfony 5.1 requires PHP 7.2.5 or higher to run.<br>
        Prefer to have MySQL 5.6 or higher.<br>
        Make sure PHP is in the Path environment variable if you are on a Windows system.<br>
        Note that PHP must have the extension mb_string activated for the slug converter to work.</p>
    <p>You need an installation of Composer.<br>
        So, install it if you don't have it. (<a href="https://getcomposer.org/"
            rel="nofollow">https://getcomposer.org/</a>)</p>
    <p>If you want to use Git (optional), install it. (<a href="https://git-scm.com/downloads"
            rel="nofollow">https://git-scm.com/downloads</a>)</p>
    <h3>Project files local deployement</h3>
    <p>Manually download the content of the Github repository to a location on your file system.<br>
        You can also use git.<br>
        In Git, go to the chosen location and execute the following command:</p>
    <pre><code>git clone https://github.com/JgPhil/Snowtricks.git .</code></pre>
    <p>Open a command console and join the application root directory.<br>
        Install dependencies by running the following command:</p>
    <pre><code>composer install</code></pre>

<h3>Database generation</h3>
<p>Change the database connection values for correct ones in the .env file.<br>
Like the following example with a snowtricks named database to create:</p>
<pre><code>DATABASE_URL=mysql://root:@127.0.0.1:3306/snowtricks?serverVersion=5.7
</code></pre>
<p>In a new console placed in the root directory of the application;<br>
Launch the creation of the database:</p>
<pre><code>php bin/console doctrine:database:create
</code></pre>
<p>Then, build the database structure using the following command:</p>
<pre><code>php bin/console doctrine:migrations:migrate
</code></pre>
<p>Finally, load the initial dataset into the database :</p>
<pre><code>php bin/console doctrine:fixtures:load
</code></pre>
<h3>Configure the mailer connection address</h3>
<p>Go to the .env file in the project root and find the next line:</p>
<pre><code>MAILER_DSN=smtp://localhost
</code></pre>
<p>Then replace it by your own connection string:</p>
<pre><code>MAILER_DSN=smtp://user:pass@smtp.example.com
</code></pre>
<p>For more info, see <a href="https://symfony.com/doc/current/mailer.html#transport-setup"
            rel="nofollow">https://symfony.com/doc/current/mailer.html#transport-setup</a></p>
<h3>Run the web application</h3>
<h4>By WebServerBundle</h4>
<p>Launch the Apache/Php runtime environment by using Symfony via the following command:</p>
<pre><code>php bin/console serve -d
</code></pre>
<p>
Then consult the URL <a href="http://localhost:8000" rel="nofollow">http://localhost:8000</a> from your browser.
</p>
<h4>By a virtualhost</h4>
<p>If you don't wan't to use WebServerBundle, you can use your Apache/Php/Mysql environment in a normal way.<br>
This by configuring a virtualhost in which to place the project.<br>
Then check <a href="http://localhost" rel="nofollow">http://localhost</a>.</p>
<h3>Admin credentials</h3>
<p>You can access to the admin page by logging in as administrator with this credentials:</p>
<ul>
    <li>username: admin</li>
    <li>password: admin</li>
</ul>
