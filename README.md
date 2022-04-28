# BkC Sign Up Portlets

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/block-cat/ssup.svg)](https://packagist.org/packages/block-cat/ssup)

**Extensia nu este publicată pe [Packagist](https://packagist.org/)!**

Aceasta este o extensie pentru înregistrarea utilizatorilor cu aceleași credențiale pentru toate portletele din [E-Moldova](https://emoldova.org/). Portletele din [E-Moldova](https://emoldova.org/) sunt următoarele:

1. [Discuții despre Moldova Digitală](https://despre.emoldova.org/)
2. [Istoria Moldovei](https://istoria.emoldova.org/)
3. [Cultura Moldovei](https://cultura.emoldova.org/)
4. [Politica în Moldova](https://politica.emoldova.org/)
5. [Resurse Educaționale](https://edu.emoldova.org/)
6. [Geografia Moldovei](https://geografia.emoldova.org/)
7. [Demografia Moldovei](https://demografia.emoldova.org/)
8. [Economia Moldovei](https://economia.emoldova.org/)
9. [Carieră în Moldova](https://cariera.emoldova.org/)
10. [Tezaurul Național Digital](https://digi.emoldova.org/)
11. [Drept](https://drept.emoldova.org/)
12. [Presa](https://presa.emoldova.org/)
13. [WELLNESS Tu îți alegi stilul de viață](https://wellness.emoldova.org/)

## Cum aceasta lucrează?

Extensia permite înregistrarea noilor utilizatori în toate portletele menționate mai sus. Indiferent din care din portlete este realizată înregistrarea, acesta va fi înregistrat în toate celelalte cu numele de utilizator, adresa de email și parola indicate în forma pentru înregistrare.

Așa cum în multe aplicații se utilizează activarea contului nou creat prin email, de asememea, și extensia dată, are prevăzută activarea contului. Pe adresa de email este trimis un mesaj cu un link de activare a contului nou creat. Prin accesarea acestui link-ului de activare, se vor activa toate conturile din toate portletele. Astfel, utilizatorii noi vor avea acces la portletele din [E-Moldova](https://emoldova.org/) cu aceleași date, fără necesitatea de ține minte multe nume de utilizatori și parole.

## Administrare

Pentru funcționarea bună a extensiei este necesar de introdus un `API token` pentru accesul din afara portletelor. Modalitatea de setare a unui `API token` este descrisă aici: [Configurare API KEY](https://docs.maicol07.it/en/flarum-api-client#configuration).
Pentru simplitate, s-a convenit ca token-ul să fie identic pentru toate portletele. De accea, la setările extensiei, pentru toate portletele, `API token`-ul trebuie să fie identic.

![](https://i.imgur.com/bzWnBUB.png)

## Instalare

Pentru instalarea extensiei trebuie executată următoarea comandă Composer:

```sh
composer require block-cat/ssup *@dev
```

## Actualizare

Pentru actualizarea extensiei trebuie executată următoarea comandă Composer:

```sh
composer update block-cat/ssup
```

## Dezinstalare

Pentru dezinstalarea extensiei trebuie executată următoarea comandă Composer:

```sh
composer remove block-cat/ssup
```

## Link-uri utile

- [Cod sursă pe GitHub](https://github.com/block-cat/ssup)
