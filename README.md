# RevolutionPi

This package provides a comprehensive abstraction for interacting with Revolution Pi hardware (and virtualized devices)
within a PHP application. It enables process image access, IO control, LED control, serial port management, and
more, with a convenient and flexible API.

The package has specific support for Laravel, and Laravel Zero. It can also be used with plain PHP, although some
functionality such as the auto-generation commands will not be available.

The package works with the built-in PHP 8.2 on the Debian Bookworm image shipped in the RevolutionPi. No extra
extensions or configuration changes are required.

## Features

- **ProcessImage**: Full read/write access to PiControl variables.
- **Trait-based interface**: Add Revolution Pi capabilities to your own app classes via the `RevolutionPi` trait.
- **LED support**: Control and read RevPi device LEDs (color, position).
- **Serial communications**: Robust non-blocking serial port abstraction for baud rate, parity, stop/data bits, flags
- **Monitor/Observer**: Register IO monitor callbacks for change detection.
- **Magic method & attribute support**: Easy, expressive access to IO variables, matching your PiCtory configuration.
- **Remote (WebSocket/RPC) and Virtual device support**.

---

## Table of Contents

- [Installation](#installation)
- [Simplified interface](#simplified-interface)
    - [Accessing IO variables](#accessing-io-variables)
    - [Monitoring IO variables](#monitoring-io-variables)
    - [Process image](#process-image-low-level)
    - [LED control](#led-control)
    - [Serial port access](#serial-port-access)
- [Low-level interface](#low-level-interface)
    - [Hardware devices (PiControl, Terminal)](#hardware-devices-picontrol-terminal)
    - [ProcessImage interface](#processimage-interface)
    - [SerialPort interface](#serialport-interface)
    - [Monitoring with custom monitors](#monitoring-with-custom-monitors)
    - [Remote module usage](#remote-module-usage)
- [Deployment](#deployment)
- [Example: Polling and running the event loop](#example-polling-and-running-the-event-loop)
- [CLI Commands](#cli-commands)
- [License](#license)

---

## Installation

**Require with Composer**

   ```
   composer require flat3/revpi
   ```

Out of the box `revpi` pulls in only the required packages.

**To include the PHP class generator**

```
composer require --dev nette/php-generator
```

**To include websocket support**

```
composer require amphp/websocket-server amphp/websocket-client
```

---

## Quick start

`flat3/revpi` makes heavy use of the Laravel service container. All concrete implementations can be resolved from
interfaces. Code should either be run *on* the RevolutionPi device, or via an IDE that can run code remotely like
PhpStorm.

Note that Laravel Zero does not use package auto-discovery. You will need to add this package's service provider to your
`config/app.php`.

```php
  use Flat3\RevPi\Interfaces\Module;
  use Flat3\RevPi\Led\LedColour;
  use Flat3\RevPi\Led\LedPosition;
  
  $pi = app(Module::class); // Resolved from the interface to the correct module type

  $pi->led(LedPosition::A1)->set(LedColour::Red);
  print_r($pi->led(LedPosition::A1)->get());
```

---

## Simplified Interface

It's recommended to generate a device-specific class from your exported PiCtory JSON. This provides IDE auto-completion
for all IO variables as strongly typed methods and documented properties.

1. **Export your configuration from the PiCtory web interface as a project file**  
   (e.g. `pictory.rsc`)

2. **Generate the PHP class**

```bash
  php artisan revpi:generate pictory.rsc MyPi
```

This creates `app/MyPi.php`.

3. **Use your class**

```php
  use Flat3\RevPi\Interfaces\Module;
  use Flat3\RevPi\Led\LedColour;
  use Flat3\RevPi\Led\LedPosition;
  use Flat3\RevPi\Monitors\DigitalMonitor;
  use Flat3\RevPi\SerialPort\BaudRate;
  use Flat3\RevPi\SerialPort\LocalFlag;
  use Illuminate\Console\Command;
  use Revolt\EventLoop;

  $pi = new \App\MyPi;
   
  // Create an instance of the serial port that loops the input back to the output
  $port = $pi->serialPort();
  $port->setSpeed(BaudRate::B576000);
  $port->clearFlag(LocalFlag::CanonicalInput);
  $port->onReadable(function (string $data) use ($port) {
      $port->write($data);
  });

  // Monitor the core temperature, writing updated values to the serial port
  $pi->Core_Temperature()->monitor(new DigitalMonitor, function ($value) use ($port) {
      $port->write($value."\n");
  });

  // Start polling
  $pi->module()->resume();

  // Start the event loop
  EventLoop::run();
```

### Accessing IO Variables

Assuming your PiCtory-exported class has an input named `input1` and an output named `output1`:

```php
$pi = new \App\MyPi;

// Read input (as property)
$level = $pi->input1; // int|bool

// Or as a method (returns InputIO object)
$input = $pi->input1();
$currentValue = $input->get();

// Read output
$currentStatus = $pi->output1;

// Set output (as property)
$pi->output1 = true;

// Or as a method (returns OutputIO object)
$pi->output1()->set(1);

// Reset output to its default value
$pi->output1()->reset();
```

### Monitoring IO Variables

The `DigitalMonitor` will cause the callback to be called whenever the monitored value changes.

```php
use Flat3\RevPi\Monitors\DigitalMonitor;

$pi = new \App\MyPi;

$pi->input1()->monitor(new DigitalMonitor, function($newValue) {
    // React to input1 changes
    logger("input1 changed: $newValue");
});
```

### Process Image (Low-Level)

Get the raw process image interface for advanced access:

```php
$image = $pi->processImage();

$value = $image->readVariable('input1');
$image->writeVariable('output1', 1);
$dump = $image->dumpImage(); // Raw string of process image data
$info = $image->getDeviceInfo(); // Information about the base module
$infoList = $image->getDeviceInfoList(); // Information about all expansion modules
```

### LED Control

```php
use Flat3\RevPi\Led\LedColour;
use Flat3\RevPi\Led\LedPosition;

// Set LED A1 to green
$pi->led(LedPosition::A1)->set(LedColour::Green);

// Get current LED color (as enum)
$current = $pi->led(LedPosition::A1)->get(); // LedColour instance

// Turn an LED off
$pi->led(LedPosition::A1)->off();
```

### Serial Port Access

```php
use Flat3\RevPi\SerialPort\BaudRate;

// Open default serial port
$port = $pi->serialPort();

// Or specify device path:
$port = $pi->serialPort('/dev/ttyRS485-1');

// Configure the port:
$port->setSpeed(BaudRate::B9600);
$port->setParity(Parity::Even);
$port->setDataBits(DataBits::CS8);

// Write and read
$port->write("Hello, RevPi!");
$response = $port->read(128); // up to 128 bytes

// Register event handler for readable data
$port->onReadable(function($data) {
    echo "Serial received: $data\n";
});

// Flush or break
$port->flush(QueueSelector::Both);
$port->break();
```

---

## Low-level interface

### Hardware Devices (PiControl, Terminal)

You can inject or instantiate the underlying devices (see `Flat3\RevPi\Interfaces\Hardware\*`) for advanced operations,
e.g. binary FFI device IO, custom IOCTLs, etc.

```php
$picontrol = app(\Flat3\RevPi\Interfaces\Hardware\PiControl::class);
$terminal = app(\Flat3\RevPi\Interfaces\Hardware\Terminal::class);
```

### ProcessImage Interface

Everything required for direct process image manipulation:

```php
$image = app(\Flat3\RevPi\Interfaces\ProcessImage::class);

$value = $image->readVariable('SomeName');
$image->writeVariable('OtherVar', 123);
```

### SerialPort Interface

Inject or use via the module interface.

```php
$port = app(\Flat3\RevPi\Interfaces\SerialPort::class); // Usually you want $pi->serialPort(...)
```

### Monitoring with Custom Monitors

If you want to create a custom monitor (beyond DigitalMonitor):

```php
use Flat3\RevPi\Interfaces\Monitor;

class MyMonitor implements Monitor {
    public function evaluate(int|bool|null $next): bool {
        // Implement custom transition/action logic here
        // e.g. if crossing a threshold, fire webhook
        // Return true if the monitor has detected sufficient change
    }
}

// Register:
$pi->module()->monitor('input1', new MyMonitor, function($newValue) {
    // Callback logic
});
```

### Remote Module Usage

You can communicate with a *remote* RevPi device via a WebSocket.

```php
use Flat3\RevPi\RevolutionPi;

$pi = new \App\MyPi;

// Adding the remote call creates a connection to a device
$pi->remote('ws://10.1.2.3:12873'); 

// From now, other methods act remotely:
$pi->output1 = 1;
$status = $pi->input2;
$pi->led(LedPosition::A1)->set(LedColour::Cyan);
```

The remote device should listen for incoming connections, use `php artisan revpi:listen` to start the server.

Out of the box the package does not provide for authentication, or encryption. These can be added by creating a more
complex websocket handshake object and passing it to the `remote` method. On the server-side, the basic `revpi:listen`
code can be modified to support encryption and authentication.

---

## Deployment

The package has been specifically designed to work with [Laravel Zero](https://laravel-zero.com). Using
the [standalone application](https://laravel-zero.com/docs/build-a-standalone-application)
feature will convert your RevolutionPi project into a single executable file that can be started automatically when the
base module boots up.

---

## Example: Polling and running the event loop

Typically you'll want to run your polling/event loop. The package provides an artisan command (see below) or call from
code:

```php
use Revolt\EventLoop;

$pi = new \App\MyPi;

$pi->repeat(1, function($pi) {
    // This is called every second
    logger("Current value is: " . $pi->input1);
});

EventLoop::run();
```

Or as a CLI command:

```bash
php artisan revpi:run
```

---

## CLI Commands

This package includes artisan commands (`php artisan revpi`):

- `revpi:generate <pictory.json> [class]`  
  Generate a typed PHP class for your device from your pictory export.

- `revpi:run`  
  Run your polling/event loop (call `resume()` and enter main loop).

- `revpi:led:get <position>`  
  Display current color of the chosen LED.

- `revpi:led:set <position> <colour>`  
  Set a given LED (positions: a1–a5; colors: off, red, green, orange, blue, magenta, cyan, white).

- `revpi:info`  
  List available device(s) and their info/status.

- `revpi:dump <file>`  
  Dump the current process image binary to a file.

- `revpi:listen [--address=0.0.0.0] [--port=12873]`  
  Run the JSON-RPC/WS server for remote connections.

---

## License

[MIT](LICENSE)  
Flat3/RevPi, 2025.

---

## Further Reading

- [Revolution Pi Documentation](https://revolutionpi.com/)
- [PiCtory Configuration Tool](https://revolutionpi.com/tutorials/pictory/)
- [Laravel Documentation](https://laravel.com/docs/)
- [Amp](https://amphp.org/)
- [FFI Manual (PHP)](https://www.php.net/manual/en/book.ffi.php)

---
