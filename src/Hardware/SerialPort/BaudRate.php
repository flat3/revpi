<?php

declare(strict_types=1);

namespace Flat3\RevPi\Hardware\SerialPort;

enum BaudRate: int
{
    case B0 = 0000000;
    case B50 = 0000001;
    case B75 = 0000002;
    case B110 = 0000003;
    case B134 = 0000004;
    case B150 = 0000005;
    case B200 = 0000006;
    case B300 = 0000007;
    case B600 = 0000010;
    case B1200 = 0000011;
    case B1800 = 0000012;
    case B2400 = 0000013;
    case B4800 = 0000014;
    case B9600 = 0000015;
    case B19200 = 0000016;
    case B38400 = 0000017;
    case B57600 = 0010001;
    case B115200 = 0010002;
    case B230400 = 0010003;
    case B460800 = 0010004;
    case B500000 = 0010005;
    case B576000 = 0010006;
    case B921600 = 0010007;
    case B1000000 = 0010010;
    case B1152000 = 0010011;
    case B1500000 = 0010012;
    case B2000000 = 0010013;
    case B2500000 = 0010014;
    case B3000000 = 0010015;
    case B3500000 = 0010016;
    case B4000000 = 0010017;
}
