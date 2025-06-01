<?php

declare(strict_types=1);

namespace Flat3\RevPi\ProcessImage;

enum ModuleType: int
{
    case KUNBUS_FW_DESCR_TYP_MG_CAN_OPEN = 71;
    case KUNBUS_FW_DESCR_TYP_MG_CCLINK = 72;
    case KUNBUS_FW_DESCR_TYP_MG_DEV_NET = 73;
    case KUNBUS_FW_DESCR_TYP_MG_ETHERCAT = 74;
    case KUNBUS_FW_DESCR_TYP_MG_ETHERNET_IP = 75;
    case KUNBUS_FW_DESCR_TYP_MG_POWERLINK = 76;
    case KUNBUS_FW_DESCR_TYP_MG_PROFIBUS = 77;
    case KUNBUS_FW_DESCR_TYP_MG_PROFINET_RT = 78;
    case KUNBUS_FW_DESCR_TYP_MG_PROFINET_IRT = 79;
    case KUNBUS_FW_DESCR_TYP_MG_CAN_OPEN_MASTER = 80;
    case KUNBUS_FW_DESCR_TYP_MG_SERCOS3 = 81;
    case KUNBUS_FW_DESCR_TYP_MG_SERIAL = 82;
    case KUNBUS_FW_DESCR_TYP_MG_PROFINET_SITARA = 83;
    case KUNBUS_FW_DESCR_TYP_MG_PROFINET_IRT_MASTER = 84;
    case KUNBUS_FW_DESCR_TYP_MG_ETHERCAT_MASTER = 85;
    case KUNBUS_FW_DESCR_TYP_MG_MODBUS_RTU = 92;
    case KUNBUS_FW_DESCR_TYP_MG_MODBUS_TCP = 93;
    case KUNBUS_FW_DESCR_TYP_PI_CORE = 95;
    case KUNBUS_FW_DESCR_TYP_PI_DIO_14 = 96;
    case KUNBUS_FW_DESCR_TYP_PI_DI_16 = 97;
    case KUNBUS_FW_DESCR_TYP_PI_DO_16 = 98;
    case KUNBUS_FW_DESCR_TYP_MG_DMX = 100;
    case KUNBUS_FW_DESCR_TYP_PI_AIO = 103;
    case KUNBUS_FW_DESCR_TYP_PI_COMPACT = 104;
    case KUNBUS_FW_DESCR_TYP_PI_CONNECT = 105;
    case KUNBUS_FW_DESCR_TYP_PI_CON_CAN = 109;
    case KUNBUS_FW_DESCR_TYP_PI_CON_MBUS = 110;
    case KUNBUS_FW_DESCR_TYP_PI_CON_BT = 111;
    case KUNBUS_FW_DESCR_TYP_PI_MIO = 118;
    case KUNBUS_FW_DESCR_TYP_PI_FLAT = 135;
    case KUNBUS_FW_DESCR_TYP_PI_CONNECT_4 = 136;
    case KUNBUS_FW_DESCR_TYP_PI_RO = 137;
    case KUNBUS_FW_DESCR_TYP_PI_CONNECT_5 = 138;

    case VIRTUAL = 32767;
    case UNKNOWN = 32768;

    public function name(): string
    {
        return match ($this) {
            self::KUNBUS_FW_DESCR_TYP_PI_CORE => 'RevPi Core',
            self::KUNBUS_FW_DESCR_TYP_PI_DIO_14 => 'RevPi DIO',
            self::KUNBUS_FW_DESCR_TYP_PI_DI_16 => 'RevPi DI',
            self::KUNBUS_FW_DESCR_TYP_PI_DO_16 => 'RevPi DO',
            self::KUNBUS_FW_DESCR_TYP_PI_AIO => 'RevPi AIO',
            self::KUNBUS_FW_DESCR_TYP_PI_COMPACT => 'RevPi Compact',
            self::KUNBUS_FW_DESCR_TYP_PI_CONNECT => 'RevPi Connect',
            self::KUNBUS_FW_DESCR_TYP_PI_CON_CAN => 'RevPi CON CAN',
            self::KUNBUS_FW_DESCR_TYP_PI_CON_MBUS => 'RevPi CON M-Bus',
            self::KUNBUS_FW_DESCR_TYP_PI_CON_BT => 'RevPi CON BT',
            self::KUNBUS_FW_DESCR_TYP_PI_MIO => 'RevPi MIO',
            self::KUNBUS_FW_DESCR_TYP_PI_FLAT => 'RevPi Flat',
            self::KUNBUS_FW_DESCR_TYP_PI_CONNECT_4 => 'RevPi Connect 4',
            self::KUNBUS_FW_DESCR_TYP_PI_RO => 'RevPi RO',
            self::KUNBUS_FW_DESCR_TYP_PI_CONNECT_5 => 'RevPi Connect 5',

            self::KUNBUS_FW_DESCR_TYP_MG_CAN_OPEN => 'Gateway CANopen',
            self::KUNBUS_FW_DESCR_TYP_MG_CCLINK => 'Gateway CC-Link',
            self::KUNBUS_FW_DESCR_TYP_MG_DEV_NET => 'Gateway DeviceNet',
            self::KUNBUS_FW_DESCR_TYP_MG_ETHERCAT => 'Gateway EtherCAT',
            self::KUNBUS_FW_DESCR_TYP_MG_ETHERNET_IP => 'Gateway EtherNet/IP',
            self::KUNBUS_FW_DESCR_TYP_MG_POWERLINK => 'Gateway Powerlink',
            self::KUNBUS_FW_DESCR_TYP_MG_PROFIBUS => 'Gateway Profibus',
            self::KUNBUS_FW_DESCR_TYP_MG_PROFINET_RT => 'Gateway Profinet RT',
            self::KUNBUS_FW_DESCR_TYP_MG_PROFINET_IRT => 'Gateway Profinet IRT',
            self::KUNBUS_FW_DESCR_TYP_MG_CAN_OPEN_MASTER => 'Gateway CANopen Master',
            self::KUNBUS_FW_DESCR_TYP_MG_SERCOS3 => 'Gateway SercosIII',
            self::KUNBUS_FW_DESCR_TYP_MG_SERIAL => 'Gateway Serial',
            self::KUNBUS_FW_DESCR_TYP_MG_ETHERCAT_MASTER => 'Gateway EtherCAT Master',
            self::KUNBUS_FW_DESCR_TYP_MG_MODBUS_RTU => 'Gateway ModbusRTU',
            self::KUNBUS_FW_DESCR_TYP_MG_MODBUS_TCP => 'Gateway ModbusTCP',
            self::KUNBUS_FW_DESCR_TYP_MG_DMX => 'Gateway DMX',

            self::VIRTUAL => 'Virtual',

            default => 'Unknown',
        };
    }
}
