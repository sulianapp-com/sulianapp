<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 22/02/2017
 * Time: 00:57
 */

namespace app\common\helpers;


use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Mlogger;
use Monolog\Processor\WebProcessor;

/**
 *
 * monolog 日志封装类
 *
 * monolog是 Laravel,Symfony,Silex 默认集成的日志库, 同时大量其他框架提供了集成扩展.
 * 它是最流行的 php log库, 自带超多handler, 长期维护, 稳定更新.
 * 它支持以各种方式记录日志: 记录到文件,mail,nosql,mail,irc,firephp,elasticsearch服务器....
 *```php
 * \app\common\helpers\Logger::debug('这是一条debug日志');
 * \app\common\helpers\Logger::info('这是一条info日志');
 * \app\common\helpers\Logger::warn('这是一条warn日志');
 * \app\common\helpers\Logger::error('这是一条error日志');
 *```
 * yunshop-monolog 默认注册的StreamHandler的日志级别为 debug.
 * 如果你想改变它的级别或者不想使用StreamHandler, 就需要先取出这个handler.
 * 假设,我们现在的在生产环境下的日志需求是这样:
 * 1. 只想在本地文件中记录Error以上级别的日志供常规检查
 * 2. info 以上的日志向发到外部的 MongoDb 数据库中,供日志监控和分析
 * 3. 不记录任何debug信息.
 *
 *```php
 * $logger = Logger::getLogger();
 * $stream_handler = $logger->popHandler();  // 取出 StreamHandler 对象
 * $stream_handler->setLevel(Logger::ERROR); // 重设其日志级别
 * $logger->pushHandler($stream_handler);    // 注册修改后的StreamHandler 对象
 * $mongodb = new MongoDBHandler(new \Mongo("mongodb://***.***.***.***:27017"), "logs", "prod", Logger::INFO);
 * $logger->pushHandler($mongodb); // 文件
 *```
 *
 * @method static Mlogger pushHandler(HandlerInterface $handler) Pushes a handler on to the stack.
 * @method static Mlogger pushProcessor(callable $callback)
 * @method static Mlogger setHandlers(array $handlers)  Set handlers, replacing all existing ones. If a map is passed, keys will be ignored.
 * @method static HandlerInterface popHandler() Pops a handler from the stack
 * @method static HandlerInterface[] getHandlers()
 * @method static callable popProcessor()
 * @method static callable[] getProcessors()
 *
 * @method static bool debug(string $message, array $context = array())
 * @method static bool info(string $message, array $context = array())
 * @method static bool notice(string $message, array $context = array())
 * @method static bool warn(string $message, array $context = array())
 * @method static bool warning(string $message, array $context = array())
 * @method static bool err(string $message, array $context = array())
 * @method static bool error(string $message, array $context = array())
 * @method static bool crit(string $message, array $context = array())
 * @method static bool critical(string $message, array $context = array())
 * @method static bool alert(string $message, array $context = array())
 * @method static bool emerg(string $message, array $context = array())
 * @method static bool emergency(string $message, array $context = array())
 *
 * @method static bool addRecord(string $level, $message, array $context = array())
 * @method static bool addDebug(string $message, array $context = array())
 * @method static bool addInfo(string $message, array $context = array())
 * @method static bool addNotice(string $message, array $context = array())
 * @method static bool addWarning(string $message, array $context = array())
 * @method static bool addError(string $message, array $context = array())
 * @method static bool addCritical(string $message, array $context = array())
 * @method static bool addAlert(string $message, array $context = array())
 * @method static bool addEmergency(string $message, array $context = array())
 *
 */
class Logger
{
    const DEBUG = 100;
    const INFO = 200;
    const NOTICE = 250;
    const WARNING = 300;
    const ERROR = 400;
    const CRITICAL = 500;
    const ALERT = 550;
    const EMERGENCY = 600;

    /** @var  Mlogger */
    static protected $logger;

    static public function init()
    {
        if (!self::$logger instanceof Mlogger) {
            self::$logger = new Mlogger('yunshop');
            //@todo 配置日志记录目录
            $handler = new StreamHandler(base_path() . '/data/logs/' . date('y_m_d') . '.log', Logger::DEBUG);
            $handler->getFormatter()->allowInlineLineBreaks();
            $handler->getFormatter()->ignoreEmptyContextAndExtra();
            self::$logger->pushProcessor(new WebProcessor());
            self::$logger->pushHandler($handler); // 文件
        }
    }

    static public function getLogger()
    {
        self::init();
        return self::$logger;
    }

    static public function __callStatic($method, $paramters)
    {
        self::init();
        if (method_exists(self::$logger, $method)) {
            return call_user_func_array(array(self::$logger, $method), $paramters);
        }
        if (method_exists('Mlogger', $method)) {
            return forward_static_call_array(array('Mlogger', $method), $paramters);
        } else {
            throw new \RuntimeException('方法不存在');
        }
    }
}