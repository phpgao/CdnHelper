<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * CDN助手
 *
 * @package CdnHelper
 * @author 老高
 * @version 0.1
 * @link http://www.phpgao.com
 */
class CdnHelper_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('CdnHelper_Plugin', 'replace');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('CdnHelper_Plugin', 'replace');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $siteUrl = Helper::options()->siteUrl;
        /** 分类名称 */
        $domain = new Typecho_Widget_Helper_Form_Element_Text('domain', null, $siteUrl, _t('图片域名'), '一般为主域名');
        $form->addInput($domain);

        $cdn = new Typecho_Widget_Helper_Form_Element_Text('cdn', null, $siteUrl, _t('CDN加速域名'), '格式请与图片域名对应，不要忘了http和最后的/');
        $form->addInput($cdn);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 插件实现方法
     *
     * @access public
     * @return string
     */
    public static function replace($content, $class, $string)
    {
        $options = Helper::options()->plugin(str_replace('_Plugin','',__CLASS__));

        $html_string = is_null($string) ? $content : $string;

        class_exists('simple_html_dom') || require_once 'simple_html_dom.php';

        $html = $html = str_get_html($html_string, 1, 1, 'UTF-8', false);
        $imgs = $html->find('img');
        if( count($imgs) == 0){
            return $html_string;
        }

        foreach($imgs as $img){
            @$img->src = str_replace($options->domain, $options->cdn, $img->src);
            @$img->{'data-url'} = str_replace($options->domain, $options->cdn, $img->{'data-url'});
        }

        return $html->save();
    }
}