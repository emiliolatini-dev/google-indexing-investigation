# FotoMoto Local Performance Checkpoint

Date: `2026-07-17`

## Safe final state

- Apache executable: `D:\XAMPP\apache\bin\httpd.exe`
- Apache configuration test: `Syntax OK`
- PHP configuration: `D:\XAMPP\php\php.ini`
- Zend OPcache is loaded for Apache and disabled for CLI
- no benchmark `curl` or PowerShell process remained active at checkpoint
- temporary web diagnostics are absent from the WordPress document root
- no database, plugin, WordPress safety, golden backup or Defender setting was changed

## Active OPcache configuration

```ini
zend_extension=opcache
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=256
opcache.interned_strings_buffer=32
opcache.max_accelerated_files=50000
opcache.validate_timestamps=1
opcache.revalidate_freq=2
opcache.save_comments=1
opcache.jit=0
opcache.optimization_level=0
```

`opcache.optimization_level=0` is deliberate. The default optimizer caused the Apache child to exit in `php8ts.dll` with Windows exception `0xC00000FD` (stack overflow). Opcode caching without optimizer passes remained stable.

## HTTP benchmark

Each endpoint was restarted separately and requested three times. Times are seconds.

| Path | Before 1 | Before 2 | Before 3 | After 1 | After 2 | After 3 |
|---|---:|---:|---:|---:|---:|---:|
| `/test-static.txt` | 7.074 / 200 | 0.109 / 200 | 0.001 / 200 | 7.801 / 200 | 0.120 / 200 | 0.001 / 200 |
| `/wp-login.php` | 127.220 / 500 | 95.627 / 200 | 120.912 / 500 | 126.573 / 500 | 51.395 / 200 | 33.560 / 200 |
| `/` | 125.137 / 500 | 119.304 / 200 | 120.636 / 500 | 126.731 / 500 | 99.184 / 200 | 49.219 / 200 |
| `/wp-json/` | 125.514 / 500 | 120.632 / 500 | 120.642 / 500 | 129.739 / 500 | 56.525 / 200 | 0.697 / 200 |

- before OPcache: 2 of 9 dynamic requests returned `200`
- after stable OPcache: 6 of 9 dynamic requests returned `200`; all second and third requests succeeded
- the cold request after a fresh restart still exceeds the unchanged 120-second PHP limit
- benchmark wall time fell from `1138.1 s` to `743.3 s`

## OPcache after benchmark

- used memory: `146,857,648` bytes of `268,435,456` bytes
- cached scripts: `3,825`
- cached keys: `7,646` of `65,407`
- hit rate: `65.378%`
- cache full: `false`
- OOM restarts: `0`
- hash restarts: `0`
- manual restarts: `0`
- wasted memory: `0`

## Filesystem, Defender, database and network

- `D:` is exFAT on mechanical HDD `ST4000LM024-2AN17V`
- WordPress contains `17,067` PHP files totaling `123,495,155` bytes
- warm PHP-file enumeration took approximately `0.93 s`
- after filesystem/Defender caches were warm, a temporary OPcache-off login completed in approximately `2.22 s`; this indicates the original 90-120 seconds were not pure PHP compilation time
- Defender real-time, behavior and IOAV protection were enabled; no exclusion was added
- Defender contribution remains plausible but unproven because no scoped exclusion A/B test was authorized or executed
- MariaDB `Slow_queries` remained `0`
- representative read-only SQL timings: autoload summary `0.0041 s`, posts count `0.0115 s`, postmeta count over 1.67 million rows `0.1846 s`
- no external Apache TCP connection was observed in the collected samples

## Artifacts

- `logs\benchmark-before-opcache.json`
- `logs\benchmark-after-opcache.json`
- `logs\opcache-web-initial.json`
- `logs\opcache-web-after-benchmark.json`
- `logs\filesystem-diagnostics.json`
- `logs\filesystem-diagnostics-opcache-cold.json`
- `logs\filesystem-diagnostics-opcache-off.json`
- `scripts\benchmark-http.ps1`
- `scripts\measure-filesystem.ps1`

## Backups and rollback

Original pre-OPcache configuration:

`D:\XAMPP\php\php.ini.20260717-201322.bak`

Unstable initial OPcache configuration, retained only for evidence:

`D:\XAMPP\php\php.ini.20260717-202338.opcache-initial.bak`

Stable OPcache configuration backup:

`D:\XAMPP\php\php.ini.20260717-205142.opcache-stable.bak`

Exact full rollback to pre-OPcache state:

1. Stop the local Apache `httpd.exe` processes.
2. Copy `D:\XAMPP\php\php.ini.20260717-201322.bak` over `D:\XAMPP\php\php.ini`.
3. Run `D:\XAMPP\apache\bin\httpd.exe -t -f D:/XAMPP/apache/conf/httpd.conf`.
4. Start `D:\XAMPP\apache\bin\httpd.exe -f D:/XAMPP/apache/conf/httpd.conf`.
5. Confirm through a temporary loopback-only web diagnostic that Zend OPcache is no longer loaded, then remove that diagnostic immediately.

## Deferred work

- no SSD migration was executed
- no Defender exclusion was added
- total working-copy/XAMPP copy-size inventory was interrupted at the safe-stop checkpoint
- recommended next checkpoint: prepare a verified copy-only SSD/junction migration plan, or run a separately authorized cold A/B test with temporary exclusions limited to `D:\XAMPP` and `D:\Web-Lab\projects\fotomoto-click`
