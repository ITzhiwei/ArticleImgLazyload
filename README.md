# PHP 使文章图片懒加载
## 介绍
没有任何依赖，可在任何环境下使用，开箱即用 
文章图片懒加载，当文章中存在大量图片时，可使用该库进行图片懒加载，看不到的图片不进行加载，节省带宽  
可以设置当距离可见区域多少PX时即进行加载图片，这样可使用户无感知
## 安装
```
1、使用 composer 命令安装：composer require lipowei/imglazyload
2、直接在 src 找到 ArticleImgLazyload.php 类库文件，直接拖到你的类目录内，include 该文件可直接使用
```
## 使用
```
use lipowei\smallTools\ArticleImgLazyload;
$ArticleImgLazyload = new ArticleImgLazyload;
$newContent = $ArticleImgLazyload->getNewContent($articleContent);
//将这个$newContent给前端使用即可
echo $newContent;
```

## 参数介绍
* $ArticleImgLazyload->imgPrefix
```
string
给图片路径加前缀，一般为APP使用，网站可忽略
一般编辑器写的文章不会给编辑器加域名的，这样抽取文章给APP渲染时无法读取图片
如：$ArticleImgLazyload->imgPrefix = 'http://www.aaa.com';
文章内容中的图片路径都在加上 http://www.aaa/com 这个前缀
```
* $ArticleImgLazyload->timeLazyload
```
int
当到达可见区域时是否延迟加载，单位毫秒，默认为0；
一般无需要设置，如果要看懒加载效果，可以设置为2000进行查看
```
* $ArticleImgLazyload->defaultImg
```
string
当图片未加载完成时显示的图片
默认：http://image2.sina.com.cn/blog/tmpl/v3/images/default_s_bmiddle.gif
```
* $ArticleImgLazyload->distance
```
int
距离可见区域多少PX时进行加载图片，默认为100
```
* $ArticleImgLazyload->blacklist
```
string
不参与懒加载的图片地址关键词
例如传入baidu，即代表图片地址含有baidu的都不会进行懒加载，排除掉 ueditor 写文章时编辑器自带的表情图
```
