<?php

//Thanks to http://blog.csdn.net/jollyjumper/article/details/9823047

namespace App\Controllers;

use App\Models\Link;
use App\Models\User;
use App\Models\Node;
use App\Models\Relay;
use App\Models\Smartline;
use App\Utils\Tools;
use App\Utils\URL;
use App\Services\Config;

/**
 *  HomeController
 */
class LinkController extends BaseController
{
    public function __construct()
    {
    }

    public static function GenerateRandomLink()
    {
        $i =0;
        for ($i = 0; $i < 10; $i++) {
            $token = Tools::genRandomChar(16);
            $Elink = Link::where("token", "=", $token)->first();
            if ($Elink == null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }

    public static function GenerateCode($type, $address, $port, $ios, $userid)
    {
        $Elink = Link::where("type", "=", $type)->where("address", "=", $address)->where("port", "=", $port)->where("ios", "=", $ios)->where("userid", "=", $userid)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = $type;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = $ios;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }





    public static function GenerateApnCode($isp, $address, $port, $userid)
    {
        $Elink = Link::where("type", "=", 6)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("isp", "=", $isp)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 6;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 1;
        $NLink->isp = $isp;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }


    public static function GenerateSurgeCode($address, $port, $userid, $geo, $method)
    {
        $Elink = Link::where("type", "=", 0)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("geo", "=", $geo)->where("method", "=", $method)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 0;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 1;
        $NLink->geo = $geo;
        $NLink->method = $method;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GenerateIosCode($address, $port, $userid, $geo, $method)
    {
        $Elink = Link::where("type", "=", -1)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("geo", "=", $geo)->where("method", "=", $method)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = -1;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 1;
        $NLink->geo = $geo;
        $NLink->method = $method;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GenerateAclCode($address, $port, $userid, $geo, $method)
    {
        $Elink = Link::where("type", "=", 9)->where("address", "=", $address)->where("port", "=", $port)->where("userid", "=", $userid)->where("geo", "=", $geo)->where("method", "=", $method)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 9;
        $NLink->address = $address;
        $NLink->port = $port;
        $NLink->ios = 0;
        $NLink->geo = $geo;
        $NLink->method = $method;
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GenerateRouterCode($userid, $without_mu)
    {
        $Elink = Link::where("type", "=", 10)->where("userid", "=", $userid)->where("geo", $without_mu)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 10;
        $NLink->address = "";
        $NLink->port = 0;
        $NLink->ios = 0;
        $NLink->geo = $without_mu;
        $NLink->method = "";
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GenerateSSRSubCode($userid, $without_mu)
    {
        $Elink = Link::where("type", "=", 11)->where("userid", "=", $userid)->where("geo", $without_mu)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 11;
        $NLink->address = "";
        $NLink->port = 0;
        $NLink->ios = 0;
        $NLink->geo = $without_mu;
        $NLink->method = "";
        $NLink->userid = $userid;
        $NLink->token = LinkController::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GetContent($request, $response, $args)
    {
        $token = $args['token'];

        //$builder->getPhrase();
        $Elink = Link::where("token", "=", $token)->first();
        if ($Elink == null) {
            return null;
        }

        switch ($Elink->type) {
            case -1:
                $user=User::where("id", $Elink->userid)->first();
                if ($user == null) {
                    return null;
                }

                $is_ss = 1;
                if (isset($request->getQueryParams()["is_ss"])) {
                    $is_ss = $request->getQueryParams()["is_ss"];
                }

                $is_mu = 0;
                if (isset($request->getQueryParams()["is_mu"])) {
                    $is_mu = $request->getQueryParams()["is_mu"];
                }

                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename=allinone.conf');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetIosConf($user, $is_mu, $is_ss));
                return $newResponse;
            case 3:
                $type = "PROXY";
                break;
            case 7:
                $type = "PROXY";
                break;
            case 6:
                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.mobileconfig');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetApn($Elink->isp, $Elink->address, $Elink->port, User::where("id", "=", $Elink->userid)->first()->pac));
                return $newResponse;
            case 0:
                if ($Elink->geo==0) {
                    $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.conf');//->getBody()->write($builder->output());
                    $newResponse->getBody()->write(LinkController::GetSurge(User::where("id", "=", $Elink->userid)->first()->passwd, $Elink->method, $Elink->address, $Elink->port, User::where("id", "=", $Elink->userid)->first()->pac));
                    return $newResponse;
                } else {
                    $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.conf');//->getBody()->write($builder->output());
                    $newResponse->getBody()->write(LinkController::GetSurgeGeo(User::where("id", "=", $Elink->userid)->first()->passwd, $Elink->method, $Elink->address, $Elink->port));
                    return $newResponse;
                }
            case 8:
                if ($Elink->ios==0) {
                    $type = "SOCKS5";
                } else {
                    $type = "SOCKS";
                }
                break;
            case 9:
                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.acl');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetAcl(User::where("id", "=", $Elink->userid)->first()));
                return $newResponse;
            case 10:
                $user=User::where("id", $Elink->userid)->first();
                if ($user == null) {
                    return null;
                }

                $is_ss = 0;
                if (isset($request->getQueryParams()["is_ss"])) {
                    $is_ss = $request->getQueryParams()["is_ss"];
                }

                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.sh');//->getBody()->write($builder->output());
                $newResponse->getBody()->write(LinkController::GetRouter(User::where("id", "=", $Elink->userid)->first(), $Elink->geo, $is_ss));
                return $newResponse;
            case 11:
                $user=User::where("id", $Elink->userid)->first();
                if ($user == null) {
                    return null;
                }

                $max = 0;
                if (isset($request->getQueryParams()["max"])) {
                    $max = (int)$request->getQueryParams()["max"];
                }

                $mu = 0;
                if (isset($request->getQueryParams()["mu"])) {
                    $mu = (int)$request->getQueryParams()["mu"];
                }

                $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename='.$token.'.txt');
                $newResponse->getBody()->write(LinkController::GetSSRSub(User::where("id", "=", $Elink->userid)->first(), $mu, $max));
                return $newResponse;
            default:
                break;
        }
        $newResponse = $response->withHeader('Content-type', ' application/x-ns-proxy-autoconfig; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate');//->getBody()->write($builder->output());
        $newResponse->getBody()->write(LinkController::GetPac($type, $Elink->address, $Elink->port, User::where("id", "=", $Elink->userid)->first()->pac));
        return $newResponse;
    }


    public static function GetGfwlistJs($request, $response, $args)
    {
        $newResponse = $response->withHeader('Content-type', ' application/x-ns-proxy-autoconfig; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename=gfwlist.js');
        ;//->getBody()->write($builder->output());
        $newResponse->getBody()->write(LinkController::GetMacPac());
        return $newResponse;
    }

    public static function GetPcConf($user, $is_mu = 0, $is_ss = 0)
    {
        $string='
	{
	"index" : 0,
	"random" : false,
	"sysProxyMode" : 0,
	"shareOverLan" : false,
	"bypassWhiteList" : false,
	"localPort" : 1080,
	"localAuthPassword" : "'.Tools::genRandomChar(26).'",
	"dns_server" : "",
	"reconnectTimes" : 4,
	"randomAlgorithm" : 0,
	"TTL" : 60,
	"connect_timeout" : 5,
	"proxyRuleMode" : 1,
	"proxyEnable" : false,
	"pacDirectGoProxy" : false,
	"proxyType" : 0,
	"proxyHost" : "",
	"proxyPort" : 0,
	"proxyAuthUser" : "",
	"proxyAuthPass" : "",
	"proxyUserAgent" : "",
	"authUser" : "",
	"authPass" : "",
	"autoBan" : false,
	"sameHostForSameTarget" : true,
	"keepVisitTime" : 180,
	"isHideTips" : true,
	"token" : {

	},
	"portMap" : {

	}
	}
		';


        $json=json_decode($string, true);
        $temparray=array();

        $items = URL::getAllItems($user, $is_mu, $is_ss);
        foreach($items as $item) {
            array_push($temparray, array("remarks"=>$item['remark'],
                                        "server"=>$item['address'],
                                        "server_port"=>$item['port'],
                                        "method"=>$item['method'],
                                        "obfs"=>$item['obfs'],
                                        "obfsparam"=>$item['obfs_param'],
                                        "remarks_base64"=>base64_encode($item['remark']),
                                        "password"=>$item['passwd'],
                                        "tcp_over_udp"=>false,
                                        "udp_over_tcp"=>false,
                                        "group"=>$item['group'],
                                        "protocol"=>$item['protocol'],
                                        "protoparam"=>$item['protocol_param'],
                                        "obfs_udp"=>false,
                                        "enable"=>true));
        }

        $json["configs"]=$temparray;
        return json_encode($json, JSON_PRETTY_PRINT);
    }


    public static function GetIosConf($user, $is_mu = 0, $is_ss = 0)
    {
        $proxy_name="";
        $proxy_group="";

        $items = URL::getAllItems($user, $is_mu, $is_ss);
        foreach($items as $item) {
            $proxy_group .= $item['remark'].' = custom,'.$item['address'].','.$item['port'].','.$item['method'].','.$item['passwd'].','.Config::get('baseUrl').'/downloads/SSEncrypt.module'.URL::getSurgeObfs($item).",tfo=true\n";
            $proxy_name .= ",".$item['remark'];
        }

        return '
//本去广告配置文件由@lhie1提供，感谢lhie1

[General]
// Auto
loglevel = notify
dns-server = system,1.2.4.8,223.5.5.5,114.114.114.114
skip-proxy = 127.0.0.1,192.168.0.0/16,10.0.0.0/8,172.16.0.0/12,100.64.0.0/10,17.0.0.0/8,localhost,*.local,e.crashlytics.com,captive.apple.com,::ffff:0:0:0:0/1,::ffff:128:0:0:0/1

// iOS
bypass-system = true
external-controller-access = lhie1@0.0.0.0:6170

// macOS
interface = 0.0.0.0
socks-interface = 0.0.0.0
port = 8888
socks-port = 8889

// Off:On | "true" or "false"
allow-wifi-access = true
collapse-policy-group-items = true
enhanced-mode-by-rule = false
exclude-simple-hostnames = true
hide-crashlytics-request = true
ipv6 = true
replica = false

[Proxy]
Direct = direct
'.$proxy_group.'

[Proxy Group]
Domestic = select,Direct,Proxy
Proxy = select,Auto,Direct'.$proxy_name.'
Others = select,Proxy,Direct
Apple Proxy = select,Direct,Proxy'.$proxy_name.'
Auto = url-test'.$proxy_name.',url = http://www.gstatic.com/generate_204,interval = 1200


[Rule]




# Apple

URL-REGEX,apple.com/cn,Apple Proxy

PROCESS-NAME,trustd,Apple Proxy

USER-AGENT,%E5%9C%B0%E5%9B%BE*,Apple Proxy
USER-AGENT,%E8%AE%BE%E7%BD%AE*,Apple Proxy
USER-AGENT,AppStore*,Apple Proxy
USER-AGENT,com.apple.appstored*,Apple Proxy
USER-AGENT,com.apple.Mobile*,Apple Proxy
USER-AGENT,com.apple.geod*,Apple Proxy
USER-AGENT,com.apple.Maps*,Apple Proxy
USER-AGENT,com.apple.trustd/*,Apple Proxy
USER-AGENT,FindMyFriends*,Apple Proxy
USER-AGENT,FMDClient*,Apple Proxy
USER-AGENT,FMFD*,Apple Proxy
USER-AGENT,fmflocatord*,Apple Proxy
USER-AGENT,geod*,Apple Proxy
USER-AGENT,i?unes*,Apple Proxy
USER-AGENT,locationd*,Apple Proxy
USER-AGENT,MacAppStore*,Apple Proxy
USER-AGENT,Maps*,Apple Proxy
USER-AGENT,MobileAsset*,Apple Proxy
USER-AGENT,Watch*,Apple Proxy
USER-AGENT,$%7BPRODUCT*,Apple Proxy
USER-AGENT,Music*,Apple Proxy
USER-AGENT,?arsecd*,Apple Proxy
USER-AGENT,securityd*,Apple Proxy
USER-AGENT,server-bag*,Apple Proxy
USER-AGENT,Settings*,Apple Proxy
USER-AGENT,Software%20Update*,Apple Proxy
USER-AGENT,SyncedDefaults*,Apple Proxy
USER-AGENT,passd*,Apple Proxy
USER-AGENT,swcd*,Apple Proxy
USER-AGENT,trustd*,Apple Proxy

DOMAIN,cn-smp-paymentservices.apple.com,Domestic
DOMAIN,support.apple.com,Apple Proxy
DOMAIN,smp-device-content.apple.com,Apple Proxy
DOMAIN,osxapps.itunes.apple.com,Apple Proxy
DOMAIN,metrics.apple.com,Apple Proxy
DOMAIN,iosapps.itunes.apple.com,Apple Proxy
DOMAIN,init.itunes.apple.com,Apple Proxy
DOMAIN,images.apple.com,Apple Proxy
DOMAIN,idmsa.apple.com,Apple Proxy
DOMAIN,guzzoni.apple.com,Apple Proxy
DOMAIN,configuration.apple.com,Apple Proxy
DOMAIN,captive.apple.com,Apple Proxy
DOMAIN,appleiphonecell.com,Apple Proxy
DOMAIN,appleid.apple.com,Apple Proxy
DOMAIN,swscan.apple.com,Apple Proxy
DOMAIN,swdist.apple.com,Apple Proxy
DOMAIN,swquery.apple.com,Apple Proxy
DOMAIN,swdownload.apple.com,Apple Proxy
DOMAIN,swcdn.apple.com,Apple Proxy
DOMAIN-SUFFIX,akadns.net,Apple Proxy
DOMAIN-SUFFIX,cdn-apple.com,Apple Proxy
DOMAIN-SUFFIX,ess.apple.com,Apple Proxy
DOMAIN-SUFFIX,lookup-api.apple.com,Apple Proxy
DOMAIN-SUFFIX,ls.apple.com,Apple Proxy
DOMAIN-SUFFIX,mzstatic.com,Apple Proxy
DOMAIN-SUFFIX,push.apple.com,Apple Proxy
DOMAIN-SUFFIX,siri.apple.com,Apple Proxy
DOMAIN-SUFFIX,aaplimg.com,Apple Proxy
DOMAIN-SUFFIX,apple.co,Apple Proxy
DOMAIN-SUFFIX,apple.com,Apple Proxy
DOMAIN-SUFFIX,icloud-content.com,Apple Proxy
DOMAIN-SUFFIX,icloud.com,Apple Proxy
DOMAIN-SUFFIX,itunes.apple.com,Apple Proxy
DOMAIN-SUFFIX,itunes.com,Apple Proxy
DOMAIN-SUFFIX,me.com,Apple Proxy



// Client
PROCESS-NAME,Jietu,REJECT

// UA
USER-AGENT,BaiduWallet*,REJECT



# Ads in Video apps

// iqiyi
DOMAIN-SUFFIX,ad.m.iqiyi.com,REJECT
DOMAIN-SUFFIX,afp.iqiyi.com,REJECT
DOMAIN-SUFFIX,api.cupid.iqiyi.com,REJECT
DOMAIN-SUFFIX,c.uaa.iqiyi.com,REJECT
DOMAIN-SUFFIX,cloudpush.iqiyi.com,REJECT
DOMAIN-SUFFIX,cm.passport.iqiyi.com,REJECT
DOMAIN-SUFFIX,emoticon.sns.iqiyi.com,REJECT
DOMAIN-SUFFIX,gamecenter.iqiyi.com,REJECT
DOMAIN-SUFFIX,hotchat-im.iqiyi.com,REJECT
DOMAIN-SUFFIX,ifacelog.iqiyi.com,REJECT
DOMAIN-SUFFIX,mbdlog.iqiyi.com,REJECT
DOMAIN-SUFFIX,msg.71.am,REJECT
DOMAIN-SUFFIX,msg.video.qiyi.com,REJECT
DOMAIN-SUFFIX,msg2.video.qiyi.com,REJECT
DOMAIN-SUFFIX,msga.cupid.iqiyi.com,REJECT
DOMAIN-SUFFIX,policy.video.iqiyi.com,REJECT
DOMAIN-SUFFIX,yuedu.iqiyi.com,REJECT
IP-CIDR,112.13.64.0/22,REJECT,no-resolve
IP-CIDR,112.253.36.0/24,REJECT,no-resolve
IP-CIDR,117.139.16.0/22,REJECT,no-resolve
IP-CIDR,117.139.18.132/22,REJECT,no-resolve
IP-CIDR,119.188.172.192/28,REJECT,no-resolve
IP-CIDR,119.188.173.0/27,REJECT,no-resolve
IP-CIDR,119.188.43.61/32,REJECT,no-resolve
IP-CIDR,123.130.122.128/28,REJECT,no-resolve
IP-CIDR,157.122.96.23/32,REJECT,no-resolve
IP-CIDR,183.221.244.0/22,REJECT,no-resolve
IP-CIDR,183.221.247.189/22,REJECT,no-resolve
IP-CIDR,27.221.89.128/28,REJECT,no-resolve
IP-CIDR,60.211.171.128/29,REJECT,no-resolve
IP-CIDR,60.211.211.1/32,REJECT,no-resolve

// Youku
DOMAIN-SUFFIX,actives.youku.com,REJECT
DOMAIN-SUFFIX,ad.api.3g.youku.com,REJECT
DOMAIN-SUFFIX,ad.api.mobile.youku.com,REJECT
DOMAIN-SUFFIX,ad.mobile.youku.com,REJECT
DOMAIN-SUFFIX,a-dxk.play.api.3g.youku.com,REJECT
DOMAIN-SUFFIX,atm.youku.com,REJECT
DOMAIN-SUFFIX,b.smartvideo.youku.com,REJECT
DOMAIN-SUFFIX,c.yes.youku.com,REJECT
DOMAIN-SUFFIX,das.api.youku.com,REJECT
DOMAIN-SUFFIX,das.mobile.youku.com,REJECT
DOMAIN-SUFFIX,dev-push.m.youku.com,REJECT
DOMAIN-SUFFIX,dl.g.youku.com,REJECT
DOMAIN-SUFFIX,dmapp.youku.com,REJECT
DOMAIN-SUFFIX,e.stat.ykimg.com,REJECT
DOMAIN-SUFFIX,gamex.mobile.youku.com,REJECT
DOMAIN-SUFFIX,guanggaoad.youku.com,REJECT
DOMAIN-SUFFIX,hudong.pl.youku.com,REJECT
DOMAIN-SUFFIX,huodong.pl.youku.com,REJECT
DOMAIN-SUFFIX,huodong.vip.youku.com,REJECT
DOMAIN-SUFFIX,hz.youku.com,REJECT
DOMAIN-SUFFIX,iyes.youku.com,REJECT
DOMAIN-SUFFIX,l.ykimg.com,REJECT
DOMAIN-SUFFIX,lstat.youku.com,REJECT
DOMAIN-SUFFIX,mobilemsg.youku.com,REJECT
DOMAIN-SUFFIX,msg.youku.com,REJECT
DOMAIN-SUFFIX,myes.youku.com,REJECT
DOMAIN-SUFFIX,p.l.youku.com,REJECT
DOMAIN-SUFFIX,passport-log.youku.com,REJECT
DOMAIN-SUFFIX,p-log.ykimg.com,REJECT
DOMAIN-SUFFIX,push.m.youku.com,REJECT
DOMAIN-SUFFIX,r.l.youku.com,REJECT
DOMAIN-SUFFIX,s.p.youku.com,REJECT
DOMAIN-SUFFIX,sdk.m.youku.com,REJECT
DOMAIN-SUFFIX,stat.youku.com,REJECT
DOMAIN-SUFFIX,statis.api.3g.youku.com,REJECT
DOMAIN-SUFFIX,store.tv.api.3g.youku.com,REJECT
DOMAIN-SUFFIX,store.xl.api.3g.youku.com,REJECT
DOMAIN-SUFFIX,tdrec.youku.com,REJECT
DOMAIN-SUFFIX,test.ott.youku.com,REJECT
DOMAIN-SUFFIX,test.sdk.m.youku.com,REJECT
DOMAIN-SUFFIX,v.l.youku.com,REJECT
DOMAIN-SUFFIX,val.api.youku.com,REJECT
DOMAIN-SUFFIX,vali.cp31.ott.cibntv.net,REJECT
DOMAIN-SUFFIX,wan.youku.com,REJECT
DOMAIN-SUFFIX,ykatr.youku.com,REJECT
DOMAIN-SUFFIX,ykrec.youku.com,REJECT















// letv
DOMAIN-SUFFIX,api.game.letvstore.com,REJECT
DOMAIN-SUFFIX,ark.letv.com,REJECT
DOMAIN-SUFFIX,dc.letv.com,REJECT
DOMAIN-SUFFIX,dev.dc.letv.com,REJECT
DOMAIN-SUFFIX,fz.letv.com,REJECT
DOMAIN-SUFFIX,g3.letv.com,REJECT
DOMAIN-SUFFIX,letv.allyes.com,REJECT
DOMAIN-SUFFIX,minisite.letv.com,REJECT
DOMAIN-SUFFIX,msg.m.letv.com,REJECT
DOMAIN-SUFFIX,n.mark.letv.com,REJECT
DOMAIN-SUFFIX,plog.dc.letv.com,REJECT
DOMAIN-SUFFIX,pro.hoye.letv.com,REJECT
DOMAIN-SUFFIX,pro.letv.com,REJECT
DOMAIN-SUFFIX,stat.letv.com,REJECT
DOMAIN-SUFFIX,static.app.m.letv.com,REJECT

// Tencent Live
DOMAIN-SUFFIX,aiseet.aa.atianqi.com,REJECT
DOMAIN-SUFFIX,aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,c.l.qq.com,REJECT
DOMAIN-SUFFIX,dp3.qq.com,REJECT
DOMAIN-SUFFIX,livep.l.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,livep.l.qq.com,REJECT
DOMAIN-SUFFIX,lives.l.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,lives.l.qq.com,REJECT
DOMAIN-SUFFIX,livew.l.qq.com,REJECT
DOMAIN-SUFFIX,mcgi.v.qq.com,REJECT
DOMAIN-SUFFIX,mdevstat.qqlive.qq.com,REJECT
DOMAIN-SUFFIX,monitor-uu.play.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,news-l.play.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,omgmta1.qq.com,REJECT
DOMAIN-SUFFIX,otheve.play.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,othstr.play.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,p.l.qq.com,REJECT
DOMAIN-SUFFIX,p-l.play.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,sdkconfig.play.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,t.l.qq.com,REJECT
DOMAIN-SUFFIX,t-l.play.aiseet.atianqi.com,REJECT
DOMAIN-SUFFIX,u.l.qq.com,REJECT
DOMAIN-SUFFIX,vmindhls.tc.qq.com,REJECT
DOMAIN-SUFFIX,vv.play.aiseet.atianqi.com,REJECT

// Youtube
DOMAIN-SUFFIX,ads.youtube.com,REJECT
DOMAIN-SUFFIX,azabu-u.ac.jp,REJECT
DOMAIN-SUFFIX,couchcoaster.jp,REJECT
DOMAIN-SUFFIX,delivery.dmkt-sp.jp,REJECT
DOMAIN-SUFFIX,ehg-youtube.hitbox.com,REJECT
DOMAIN-SUFFIX,m-78.jp,REJECT
DOMAIN-SUFFIX,nichibenren.or.jp,REJECT
DOMAIN-SUFFIX,nicorette.co.kr,REJECT
DOMAIN-SUFFIX,ssl-youtube.2cnt.net,REJECT
DOMAIN-SUFFIX,youtube.112.2o7.net,REJECT
DOMAIN-SUFFIX,youtube.2cnt.net,REJECT

// Sohu
DOMAIN-SUFFIX,888.tv.sohu.com,REJECT
DOMAIN-SUFFIX,agn.aty.sohu.com,REJECT
DOMAIN-SUFFIX,api.k.sohu.com,REJECT
DOMAIN-SUFFIX,aty.sohu.com,REJECT
DOMAIN-SUFFIX,bd.hd.sohu.com,REJECT
DOMAIN-SUFFIX,click.hd.sohu.com.cn,REJECT
DOMAIN-SUFFIX,click2.hd.sohu.com,REJECT
DOMAIN-SUFFIX,count.vrs.sohu.com,REJECT
DOMAIN-SUFFIX,ctr.hd.sohu.com,REJECT
DOMAIN-SUFFIX,download.wan.sohu.com,REJECT
DOMAIN-SUFFIX,goto.sms.sohu.com,REJECT
DOMAIN-SUFFIX,hui.sohu.com,REJECT
DOMAIN-SUFFIX,i.go.sohu.com,REJECT
DOMAIN-SUFFIX,images.sohu.com,REJECT
DOMAIN-SUFFIX,info.lm.tv.sohu.com,REJECT
DOMAIN-SUFFIX,lm.tv.sohu.com,REJECT
DOMAIN-SUFFIX,m.aty.sohu.com,REJECT
DOMAIN-SUFFIX,mb.hd.sohu.com.cn,REJECT
DOMAIN-SUFFIX,mmg.aty.sohu.com,REJECT
DOMAIN-SUFFIX,pb.hd.sohu.com.cn,REJECT
DOMAIN-SUFFIX,push.tv.sohu.com,REJECT
DOMAIN-SUFFIX,pv.hd.sohu.com,REJECT
DOMAIN-SUFFIX,pv.ott.hd.sohu.com,REJECT
DOMAIN-SUFFIX,pv.sohu.com,REJECT
DOMAIN-SUFFIX,s.go.sohu.com,REJECT
DOMAIN-SUFFIX,score.my.tv.sohu.com,REJECT
DOMAIN-SUFFIX,sohu.wrating.com,REJECT
DOMAIN-SUFFIX,um.hd.sohu.com,REJECT
DOMAIN-SUFFIX,vstat.v.blog.sohu.com,REJECT
DOMAIN-SUFFIX,wl.hd.sohu.com,REJECT
DOMAIN-SUFFIX,yule.sohu.com,REJECT
DOMAIN-SUFFIX,zz.m.sohu.com,REJECT

// pptv
DOMAIN-SUFFIX,asimgs.pplive.cn,REJECT
DOMAIN-SUFFIX,de.as.pptv.com,REJECT
DOMAIN-SUFFIX,jp.as.pptv.com,REJECT
DOMAIN-SUFFIX,pp2.pptv.com,REJECT
DOMAIN-SUFFIX,stat.pptv.com,REJECT
DOMAIN-SUFFIX,afp.pplive.com,REJECT

// Other
DOMAIN-SUFFIX,ad.video.51togic.com,REJECT
DOMAIN-SUFFIX,ads.cdn.tvb.com,REJECT
DOMAIN-SUFFIX,biz5.kankan.com,REJECT
DOMAIN-SUFFIX,c.algovid.com,REJECT
DOMAIN-SUFFIX,cc.xtgreat.com,REJECT
DOMAIN-SUFFIX,cm.zhiziyun.com,REJECT
DOMAIN-SUFFIX,cms.laifeng.com,REJECT
DOMAIN-SUFFIX,d.dsp.imageter.com,REJECT
DOMAIN-SUFFIX,da.mmarket.com,REJECT
DOMAIN-SUFFIX,data.vod.itc.cn,REJECT
DOMAIN-SUFFIX,dotcounter.douyutv.com,REJECT
DOMAIN-SUFFIX,files.adform.net,REJECT
DOMAIN-SUFFIX,float.kankan.com,REJECT
DOMAIN-SUFFIX,g.uusee.com,REJECT
DOMAIN-SUFFIX,game.pps.tv,REJECT
DOMAIN-SUFFIX,gcdn.2mdn.net,REJECT
DOMAIN-SUFFIX,gentags.net,REJECT
DOMAIN-SUFFIX,gg.jtertp.com,REJECT
DOMAIN-SUFFIX,gug.ku6cdn.com,REJECT
DOMAIN-SUFFIX,hp.smiler-ad.com,REJECT
DOMAIN-SUFFIX,kooyum.com,REJECT
DOMAIN-SUFFIX,kwflvcdn.000dn.com,REJECT
DOMAIN-SUFFIX,l.fancyapi.com,REJECT
DOMAIN-SUFFIX,l.ftx.fancyapi.com,REJECT
DOMAIN-SUFFIX,ld.kuaigames.com,REJECT
DOMAIN-SUFFIX,logger.baofeng.com,REJECT
DOMAIN-SUFFIX,logic.cpm.cm.kankan.com,REJECT
DOMAIN-SUFFIX,logstat.t.sfht.com,REJECT
DOMAIN-SUFFIX,match.rtbidder.net,REJECT
DOMAIN-SUFFIX,mixer.cupid.ptqy.gitv.tv,REJECT
DOMAIN-SUFFIX,mp4.res.hunantv.com,REJECT
DOMAIN-SUFFIX,msg.c002.ottcn.com,REJECT
DOMAIN-SUFFIX,msga.ptqy.gitv.tv,REJECT
DOMAIN-SUFFIX,njwxh.com,REJECT
DOMAIN-SUFFIX,nl.rcd.ptqy.gitv.tv,REJECT
DOMAIN-SUFFIX,n-st.vip.com,REJECT
DOMAIN-SUFFIX,pb.bi.gitv.tv,REJECT
DOMAIN-SUFFIX,pop.uusee.com,REJECT
DOMAIN-SUFFIX,pq.stat.ku6.com,REJECT
DOMAIN-SUFFIX,qchannel01.cn,REJECT
DOMAIN-SUFFIX,rd.kuaigames.com,REJECT
DOMAIN-SUFFIX,shizen-no-megumi.com,REJECT
DOMAIN-SUFFIX,shrek.6.cn,REJECT
DOMAIN-SUFFIX,simba.6.cn,REJECT
DOMAIN-SUFFIX,st.vq.ku6.cn,REJECT
DOMAIN-SUFFIX,statcounter.com,REJECT
DOMAIN-SUFFIX,static.bshare.cn,REJECT
DOMAIN-SUFFIX,static.duoshuo.com,REJECT
DOMAIN-SUFFIX,static.g.ppstream.com,REJECT
DOMAIN-SUFFIX,static.ku6.com,REJECT
DOMAIN-SUFFIX,static8.pmadx.com,REJECT
DOMAIN-SUFFIX,store.ptqy.gitv.tv,REJECT
DOMAIN-SUFFIX,stuff.cdn.biddingx.com,REJECT
DOMAIN-SUFFIX,t.cr-nielsen.com,REJECT
DOMAIN-SUFFIX,t7z.cupid.ptqy.gitv.tv,REJECT
DOMAIN-SUFFIX,traffic.uusee.com,REJECT
DOMAIN-SUFFIX,union.6.cn,REJECT
DOMAIN-SUFFIX,w.fancyapi.com,REJECT
DOMAIN-SUFFIX,wa.gtimg.com,REJECT
DOMAIN-SUFFIX,www.bfshan.cn,REJECT
DOMAIN-SUFFIX,x.da.hunantv.com,REJECT
DOMAIN-SUFFIX,xs.houyi.baofeng.net,REJECT



# reject

// 163
DOMAIN-SUFFIX,a.youdao.com,REJECT
DOMAIN-SUFFIX,ad.yixin.im,REJECT
DOMAIN-SUFFIX,adgeo.163.com,REJECT
DOMAIN-SUFFIX,analytics.126.net,REJECT
DOMAIN-SUFFIX,analytics.hz.netease.com,REJECT
DOMAIN-SUFFIX,analytics.ws.126.net,REJECT
DOMAIN-SUFFIX,b.bst.126.net,REJECT
DOMAIN-SUFFIX,bobo.163.com,REJECT
DOMAIN-SUFFIX,c.youdao.com,REJECT
DOMAIN-SUFFIX,clkservice.youdao.com,REJECT
DOMAIN-SUFFIX,conv.youdao.com,REJECT
DOMAIN-SUFFIX,crashlytics.163.com,REJECT
DOMAIN-SUFFIX,dsp.youdao.com,REJECT
DOMAIN-SUFFIX,dsp-impr2.youdao.com,REJECT
DOMAIN-SUFFIX,fa.163.com,REJECT
DOMAIN-SUFFIX,g.163.com,REJECT
DOMAIN-SUFFIX,g1.163.com,REJECT
DOMAIN-SUFFIX,gb.corp.163.com,REJECT
DOMAIN-SUFFIX,gorgon.youdao.com,REJECT
DOMAIN-SUFFIX,haitaoad.nosdn.127.net,REJECT
DOMAIN-SUFFIX,img1.126.net,REJECT
DOMAIN-SUFFIX,img2.126.net,REJECT
DOMAIN-SUFFIX,impservice.chnl.youdao.com,REJECT
DOMAIN-SUFFIX,impservice.dict.youdao.com,REJECT
DOMAIN-SUFFIX,impservice.dictapp.youdao.com,REJECT
DOMAIN-SUFFIX,impservice.dictvista.youdao.com,REJECT
DOMAIN-SUFFIX,impservice.dictweb.youdao.com,REJECT
DOMAIN-SUFFIX,impservice.dictword.youdao.com,REJECT
DOMAIN-SUFFIX,impservice.mail.youdao.com,REJECT
DOMAIN-SUFFIX,impservice.union.youdao.com,REJECT
DOMAIN-SUFFIX,impservice2.youdao.com,REJECT
DOMAIN-SUFFIX,impservicetest.dictapp.youdao.com,REJECT
DOMAIN-SUFFIX,ir.mail.126.com,REJECT
DOMAIN-SUFFIX,ir.mail.163.com,REJECT
DOMAIN-SUFFIX,ir.mail.yeah.net,REJECT
DOMAIN-SUFFIX,irpmt.mail.163.com,REJECT
DOMAIN-SUFFIX,mimg.126.net,REJECT
DOMAIN-SUFFIX,mr.da.netease.com,REJECT
DOMAIN-SUFFIX,nc004x.corp.youdao.com,REJECT
DOMAIN-SUFFIX,nc045x.corp.youdao.com,REJECT
DOMAIN-SUFFIX,nex.163.com,REJECT
DOMAIN-SUFFIX,oimagea2.ydstatic.com,REJECT
DOMAIN-SUFFIX,pagechoice.net,REJECT
DOMAIN-SUFFIX,pr.da.netease.com,REJECT
DOMAIN-SUFFIX,prom.gome.com.cn,REJECT
DOMAIN-SUFFIX,push.126.net,REJECT
DOMAIN-SUFFIX,push.yuedu.163.com,REJECT
DOMAIN-SUFFIX,qt002x.corp.youdao.com,REJECT
DOMAIN-SUFFIX,r.mail.163.com,REJECT
DOMAIN-SUFFIX,rd.da.netease.com,REJECT
DOMAIN-SUFFIX,rlogs.youdao.com,REJECT
DOMAIN-SUFFIX,shared.youdao.com,REJECT
DOMAIN-SUFFIX,stat.ws.126.net,REJECT
DOMAIN-SUFFIX,static.flv.uuzuonline.com,REJECT
DOMAIN-SUFFIX,statis.push.netease.com,REJECT
DOMAIN-SUFFIX,tb060x.corp.youdao.com,REJECT
DOMAIN-SUFFIX,tb104x.corp.youdao.com,REJECT
DOMAIN-SUFFIX,union.youdao.com,REJECT
DOMAIN-SUFFIX,wanproxy.127.net,REJECT
DOMAIN-SUFFIX,wr.da.netease.com,REJECT
DOMAIN-SUFFIX,www.qchannel01.cn,REJECT
DOMAIN-SUFFIX,www.qchannel02.cn,REJECT
DOMAIN-SUFFIX,www.qchannel03.cn,REJECT
DOMAIN-SUFFIX,www.qchannel04.cn,REJECT
DOMAIN-SUFFIX,ydpushserver.youdao.com,REJECT

// 10000
DOMAIN-SUFFIX,daohang.114so.cn,REJECT
DOMAIN-SUFFIX,www.114so.cn,REJECT

// 10086
DOMAIN-SUFFIX,hivedata.cc,REJECT
DOMAIN-SUFFIX,navi.gd.chinamobile.com,REJECT

// 17173
DOMAIN-SUFFIX,cvda.17173.com,REJECT
DOMAIN-SUFFIX,imgapp.yeyou.com,REJECT
DOMAIN-SUFFIX,log1.17173.com,REJECT
DOMAIN-SUFFIX,s.17173cdn.com,REJECT
DOMAIN-SUFFIX,ue.yeyoucdn.com,REJECT
DOMAIN-SUFFIX,vda.17173.com,REJECT

// 178
DOMAIN-SUFFIX,analytics.wanmei.com,REJECT
DOMAIN-SUFFIX,att.stargame.com,REJECT
DOMAIN-SUFFIX,gg.stargame.com,REJECT

// 2345
DOMAIN-SUFFIX,dl.2345.com,REJECT
DOMAIN-SUFFIX,download.2345.com,REJECT
DOMAIN-SUFFIX,jifendownload.2345.cn,REJECT
DOMAIN-SUFFIX,wan.2345.com,REJECT

// 360
DOMAIN-SUFFIX,3600.com,REJECT
DOMAIN-SUFFIX,ad.dev.360.cn,REJECT
DOMAIN-SUFFIX,ad.huajiao.com,REJECT
DOMAIN-SUFFIX,appjiagu.com,REJECT
DOMAIN-SUFFIX,cpull.360.cn,REJECT
DOMAIN-SUFFIX,dev.tg.wan.360.cn,REJECT
DOMAIN-SUFFIX,display.360totalsecurity.com,REJECT
DOMAIN-SUFFIX,down.360safe.com,REJECT
DOMAIN-SUFFIX,gamebox.360.cn,REJECT
DOMAIN-SUFFIX,hot.m.shouji.360tpcdn.com,REJECT
DOMAIN-SUFFIX,hs.qhupdate.com,REJECT
DOMAIN-SUFFIX,huid.ad.360.cn,REJECT
DOMAIN-SUFFIX,jiagu.360.cn,REJECT
DOMAIN-SUFFIX,kuaikan.netmon.360safe.com,REJECT
DOMAIN-SUFFIX,leak.360.cn,REJECT
DOMAIN-SUFFIX,mbrowser.news.haosou.com,REJECT
DOMAIN-SUFFIX,mbrowser.news.so.com,REJECT
DOMAIN-SUFFIX,mbs.hao.360.cn,REJECT
DOMAIN-SUFFIX,msoftdl.360.cn,REJECT
DOMAIN-SUFFIX,openbox.mobilem.360.cn,REJECT
DOMAIN-SUFFIX,ps.dev.360.cn,REJECT
DOMAIN-SUFFIX,pub.se.360.cn,REJECT
DOMAIN-SUFFIX,rd.wan.360.cn,REJECT
DOMAIN-SUFFIX,s.360.cn,REJECT
DOMAIN-SUFFIX,s.qhupdate.com,REJECT
DOMAIN-SUFFIX,s.union.360.cn,REJECT
DOMAIN-SUFFIX,sh.qihoo.com,REJECT
DOMAIN-SUFFIX,shouji.360.cn,REJECT
DOMAIN-SUFFIX,soft.data.weather.360.cn,REJECT
DOMAIN-SUFFIX,stat.360safe.com,REJECT
DOMAIN-SUFFIX,stat.m.360.cn,REJECT
DOMAIN-SUFFIX,stat.sd.360.cn,REJECT
DOMAIN-SUFFIX,static.ts.360.com,REJECT
DOMAIN-SUFFIX,tf.360.cn,REJECT
DOMAIN-SUFFIX,top.h.qhimg.com,REJECT
DOMAIN-SUFFIX,update.360safe.com,REJECT

// 58
DOMAIN-SUFFIX,58.xgo.com.cn,REJECT
DOMAIN-SUFFIX,brandshow.58.com,REJECT
DOMAIN-SUFFIX,imp.xgo.com.cn,REJECT
DOMAIN-SUFFIX,jing.58.com,REJECT
DOMAIN-SUFFIX,jump.luna.58.com,REJECT
DOMAIN-SUFFIX,stat.xgo.com.cn,REJECT
DOMAIN-SUFFIX,track.58.com,REJECT

// Ali
DOMAIN-SUFFIX,a.alimama.cn,REJECT
DOMAIN-SUFFIX,acjs.aliyun.com,REJECT
DOMAIN-SUFFIX,adash.m.taobao.com,REJECT
DOMAIN-SUFFIX,adashbc.m.taobao.com,REJECT
DOMAIN-SUFFIX,adash-c.ut.taobao.com,REJECT
DOMAIN-SUFFIX,adashx.m.taobao.com,REJECT
DOMAIN-SUFFIX,adashx4yt.m.taobao.com,REJECT
DOMAIN-SUFFIX,adashxgc.ut.taobao.com,REJECT
DOMAIN-SUFFIX,adsh.m.taobao.com,REJECT
DOMAIN-SUFFIX,afp.adchina.com,REJECT
DOMAIN-SUFFIX,afp.alicdn.com,REJECT
DOMAIN-SUFFIX,agoodm.m.taobao.com,REJECT
DOMAIN-SUFFIX,agoodm.wapa.taobao.com,REJECT
DOMAIN-SUFFIX,alipaylog.com,REJECT
DOMAIN-SUFFIX,amdc.alipay.com,REJECT
DOMAIN-SUFFIX,api.wapa.taobao.com,REJECT
DOMAIN-SUFFIX,api.waptest.taobao.com,REJECT
DOMAIN-SUFFIX,appdownload.alicdn.com,REJECT
DOMAIN-SUFFIX,atanx.alicdn.com,REJECT
DOMAIN-SUFFIX,atanx2.alicdn.com,REJECT
DOMAIN-SUFFIX,cdn0.mobmore.com,REJECT
DOMAIN-SUFFIX,click.aliyun.com,REJECT
DOMAIN-SUFFIX,click.mz.simba.taobao.com,REJECT
DOMAIN-SUFFIX,cm.ipinyou.com,REJECT
DOMAIN-SUFFIX,cm.mlt01.com,REJECT
DOMAIN-SUFFIX,dsp.simba.taobao.com,REJECT
DOMAIN-SUFFIX,err.taobao.com,REJECT
DOMAIN-SUFFIX,ex.mobmore.com,REJECT
DOMAIN-SUFFIX,ex.puata.info,REJECT
DOMAIN-SUFFIX,fav.simba.taobao.com,REJECT
DOMAIN-SUFFIX,feedback.whalecloud.com,REJECT
DOMAIN-SUFFIX,ff.win.taobao.com,REJECT
DOMAIN-SUFFIX,fm.p0y.cn,REJECT
DOMAIN-SUFFIX,g.click.taobao.com,REJECT
DOMAIN-SUFFIX,g.tbcdn.cn,REJECT
DOMAIN-SUFFIX,gma.alicdn.com,REJECT
DOMAIN-SUFFIX,gtms01.alicdn.com,REJECT
DOMAIN-SUFFIX,gtms02.alicdn.com,REJECT
DOMAIN-SUFFIX,gtms03.alicdn.com,REJECT
DOMAIN-SUFFIX,gtms04.alicdn.com,REJECT
DOMAIN-SUFFIX,hydra.alibaba.com,REJECT
DOMAIN-SUFFIX,hz.pre.tbusergw.taobao.net,REJECT
DOMAIN-SUFFIX,hz.tbusergw.taobao.net,REJECT
DOMAIN-SUFFIX,i.ipinyou.com,REJECT
DOMAIN-SUFFIX,init.phpwind.com,REJECT
DOMAIN-SUFFIX,intl.wapa.taobao.com,REJECT
DOMAIN-SUFFIX,intl.waptest.taobao.com,REJECT
DOMAIN-SUFFIX,jxlog.istreamsche.com,REJECT
DOMAIN-SUFFIX,log.umtrack.com,REJECT
DOMAIN-SUFFIX,m.intl.taobao.com,REJECT
DOMAIN-SUFFIX,m.simba.taobao.com,REJECT
DOMAIN-SUFFIX,match.p4p.1688.com,REJECT
DOMAIN-SUFFIX,material.istreamsche.com,REJECT
DOMAIN-SUFFIX,mlt01.com,REJECT
DOMAIN-SUFFIX,osfota.cdn.aliyun.com,REJECT
DOMAIN-SUFFIX,osupdate.aliyun.com,REJECT
DOMAIN-SUFFIX,osupdateservice.yunos.com,REJECT
DOMAIN-SUFFIX,pindao.huoban.taobao.com,REJECT
DOMAIN-SUFFIX,push.wandoujia.com,REJECT
DOMAIN-SUFFIX,re.m.taobao.com,REJECT
DOMAIN-SUFFIX,re.taobao.com,REJECT
DOMAIN-SUFFIX,redirect.simba.taobao.com,REJECT
DOMAIN-SUFFIX,rj.m.taobao.co,REJECT
DOMAIN-SUFFIX,rj.m.taobao.com,REJECT
DOMAIN-SUFFIX,sdkinit.taobao.com,REJECT
DOMAIN-SUFFIX,show.re.taobao.com,REJECT
DOMAIN-SUFFIX,simaba.taobao.com,REJECT
DOMAIN-SUFFIX,simba.m.taobao.com,REJECT
DOMAIN-SUFFIX,srd.simba.taobao.com,REJECT
DOMAIN-SUFFIX,stats.ipinyou.com,REJECT
DOMAIN-SUFFIX,strip.taobaocdn.com,REJECT
DOMAIN-SUFFIX,tanxlog.istreamsche.com,REJECT
DOMAIN-SUFFIX,tejia.taobao.com,REJECT
DOMAIN-SUFFIX,temai.taobao.com,REJECT
DOMAIN-SUFFIX,tns.simba.taobao.com,REJECT
DOMAIN-SUFFIX,tongji.linezing.com,REJECT
DOMAIN-SUFFIX,tvupgrade.yunos.com,REJECT
DOMAIN-SUFFIX,tyh.taobao.com,REJECT
DOMAIN-SUFFIX,userimg.qunar.com,REJECT
DOMAIN-SUFFIX,w.m.taobao.com,REJECT
DOMAIN-SUFFIX,yiliao.hupan.com,REJECT

// Apple
DOMAIN-SUFFIX,adserver.unityads.unity3d.com,REJECT

// Auto home
DOMAIN-SUFFIX,33.autohome.com.cn,REJECT
DOMAIN-SUFFIX,activity.app.autohome.com.cn,REJECT
DOMAIN-SUFFIX,adm0.autoimg.cn,REJECT
DOMAIN-SUFFIX,adm1.autoimg.cn,REJECT
DOMAIN-SUFFIX,adm2.autoimg.cn,REJECT
DOMAIN-SUFFIX,adm3.autoimg.cn,REJECT
DOMAIN-SUFFIX,adnewnc.app.autohome.com.cn,REJECT
DOMAIN-SUFFIX,al.autohome.com.cn,REJECT
DOMAIN-SUFFIX,applogapi.autohome.com.cn,REJECT
DOMAIN-SUFFIX,at.mct01.com,REJECT
DOMAIN-SUFFIX,c.autohome.com.cn,REJECT
DOMAIN-SUFFIX,cmx.autohome.com.cn,REJECT
DOMAIN-SUFFIX,comm.app.autohome.com.cn,REJECT
DOMAIN-SUFFIX,dspmnt.autohome.com.cn,REJECT
DOMAIN-SUFFIX,h.pcd.autohome.com.cn,REJECT
DOMAIN-SUFFIX,pcd.autohome.com.cn,REJECT
DOMAIN-SUFFIX,pmptrack-autohome.gentags.net,REJECT
DOMAIN-SUFFIX,public.app.autohome.com.cn,REJECT
DOMAIN-SUFFIX,push.app.autohome.com.cn,REJECT
DOMAIN-SUFFIX,pv.alert.autohome.com.cn,REJECT
DOMAIN-SUFFIX,pvx.autohome.com.cn,REJECT
DOMAIN-SUFFIX,rd.autohome.com.cn,REJECT
DOMAIN-SUFFIX,rdx.autohome.com.cn,REJECT
DOMAIN-SUFFIX,stats.autohome.com.cn,REJECT

// Baidu
DOMAIN-SUFFIX,a.baidu.com,REJECT
DOMAIN-SUFFIX,adm.baidu.com,REJECT
DOMAIN-SUFFIX,adscdn.baidu.com,REJECT
DOMAIN-SUFFIX,api.youqian.baidu.com,REJECT
DOMAIN-SUFFIX,as.baidu.com,REJECT
DOMAIN-SUFFIX,ashifen.com,REJECT
DOMAIN-SUFFIX,baidustatic.com,REJECT
DOMAIN-SUFFIX,baidutv.baidu.com,REJECT
DOMAIN-SUFFIX,baikebcs.bdimg.com,REJECT
DOMAIN-SUFFIX,bar.baidu.com,REJECT
DOMAIN-SUFFIX,bdimg.share.baidu.com,REJECT
DOMAIN-SUFFIX,boscdn.bpc.baidu.com,REJECT
DOMAIN-SUFFIX,c.baidu.com,REJECT
DOMAIN-SUFFIX,cb.baidu.com,REJECT
DOMAIN-SUFFIX,cbjs.baidu.com,REJECT
DOMAIN-SUFFIX,cbjslog.baidu.com,REJECT
DOMAIN-SUFFIX,cjhq.baidu.com,REJECT
DOMAIN-SUFFIX,cpro.baidu.com,REJECT
DOMAIN-SUFFIX,cpro.tieba.baidu.com,REJECT
DOMAIN-SUFFIX,cpro.zhidao.baidu.com,REJECT
DOMAIN-SUFFIX,drmcmm.baidu.com,REJECT
DOMAIN-SUFFIX,e.baidu.com,REJECT
DOMAIN-SUFFIX,eiv.baidu.com,REJECT
DOMAIN-SUFFIX,focusbaiduafp.allyes.com,REJECT
DOMAIN-SUFFIX,hc.baidu.com,REJECT
DOMAIN-SUFFIX,hm.baidu.com,REJECT
DOMAIN-SUFFIX,hmma.baidu.com,REJECT
DOMAIN-SUFFIX,hpd.baidu.com,REJECT
DOMAIN-SUFFIX,imageplus.baidu.com,REJECT
DOMAIN-SUFFIX,itsdata.map.baidu.com,REJECT
DOMAIN-SUFFIX,log.nuomi.com,REJECT
DOMAIN-SUFFIX,log.waimai.baidu.com,REJECT
DOMAIN-SUFFIX,ma.baidu.com,REJECT
DOMAIN-SUFFIX,mg09.zhaopin.com,REJECT
DOMAIN-SUFFIX,mobads.baidu.com,REJECT
DOMAIN-SUFFIX,mobads-logs.baidu.com,REJECT
DOMAIN-SUFFIX,nsclick.baidu.com,REJECT
DOMAIN-SUFFIX,rj.baidu.com,REJECT
DOMAIN-SUFFIX,shifen.com,REJECT
DOMAIN-SUFFIX,spcode.baidu.com,REJECT
DOMAIN-SUFFIX,static.map.bdimg.com,REJECT
DOMAIN-SUFFIX,static.su.baidu.com,REJECT
DOMAIN-SUFFIX,tk.baidu.com,REJECT
DOMAIN-SUFFIX,tuisong.baidu.com,REJECT
DOMAIN-SUFFIX,ucstat.baidu.com,REJECT
DOMAIN-SUFFIX,ufosdk.baidu.com,REJECT
DOMAIN-SUFFIX,ulog.imap.baidu.com,REJECT
DOMAIN-SUFFIX,union.baidu.com,REJECT
DOMAIN-SUFFIX,utility.baidu.com,REJECT
DOMAIN-SUFFIX,utk.baidu.com,REJECT
DOMAIN-SUFFIX,wangmeng.baidu.com,REJECT
DOMAIN-SUFFIX,wm.baidu.com,REJECT
DOMAIN-SUFFIX,wn.pos.baidu.com,REJECT
DOMAIN-SUFFIX,zhanzhang.baidu.com,REJECT
DOMAIN-SUFFIX,znsv.baidu.com,REJECT
DOMAIN-SUFFIX,zz.bdstatic.com,REJECT

// Book
DOMAIN-SUFFIX,70e.com,REJECT
DOMAIN-SUFFIX,ad.zhangyue.com,REJECT
DOMAIN-SUFFIX,adm.easou.com,REJECT
DOMAIN-SUFFIX,assets.easou.com,REJECT
DOMAIN-SUFFIX,cj.qidian.com,REJECT
DOMAIN-SUFFIX,da.hxspc.com,REJECT
DOMAIN-SUFFIX,drdwy.com,REJECT
DOMAIN-SUFFIX,e701.net,REJECT
DOMAIN-SUFFIX,ethod.gzgmjcx.com,REJECT
DOMAIN-SUFFIX,game.qidian.com,REJECT
DOMAIN-SUFFIX,jisucn.com,REJECT
DOMAIN-SUFFIX,m.12306media.com,REJECT
DOMAIN-SUFFIX,o.if.qidian.com,REJECT
DOMAIN-SUFFIX,picture.duokan.com,REJECT
DOMAIN-SUFFIX,push.zhangyue.com,REJECT
DOMAIN-SUFFIX,s1.cmfu.com,REJECT
DOMAIN-SUFFIX,sanya1.com,REJECT
DOMAIN-SUFFIX,stats.magicwindow.cn,REJECT
DOMAIN-SUFFIX,tjlog.easou.com,REJECT
DOMAIN-SUFFIX,tjlog.ps.easou.com,REJECT
DOMAIN-SUFFIX,tongji.qidian.com,REJECT
DOMAIN-SUFFIX,ut2.shuqistat.com,REJECT
DOMAIN-SUFFIX,xyrkl.com,REJECT
DOMAIN-SUFFIX,zhuanfakong.com,REJECT

// Caiyun
DOMAIN-SUFFIX,ad.caiyunapp.com,REJECT

// Dangdang
DOMAIN-SUFFIX,a.dangdang.com,REJECT
DOMAIN-SUFFIX,click.dangdang.com,REJECT
DOMAIN-SUFFIX,schprompt.dangdang.com,REJECT
DOMAIN-SUFFIX,t.dangdang.com,REJECT

// Duomi
DOMAIN-SUFFIX,ad.duomi.com,REJECT
DOMAIN-SUFFIX,boxshows.com,REJECT

// Facebook
DOMAIN-SUFFIX,staticxx.facebook.com,REJECT

// ele
DOMAIN-SUFFIX,app-monitor.ele.me,REJECT
DOMAIN-SUFFIX,client-api.ele.me,REJECT
DOMAIN-SUFFIX,grand.ele.me,REJECT
DOMAIN-SUFFIX,mobile-pubt.ele.me,REJECT
DOMAIN-SUFFIX,newton-api.ele.me,REJECT

// Ganji
DOMAIN-SUFFIX,ad.ganji.com,REJECT
DOMAIN-SUFFIX,analytics.ganji.com,REJECT
DOMAIN-SUFFIX,click.ganji.com,REJECT
DOMAIN-SUFFIX,ganjituiguang.ganji.com,REJECT
DOMAIN-SUFFIX,sta.ganji.com,REJECT
DOMAIN-SUFFIX,tralog.ganji.com,REJECT

// Google
DOMAIN-SUFFIX,2mdn.net,REJECT
DOMAIN-SUFFIX,ads.gmodules.com,REJECT
DOMAIN-SUFFIX,ads.google.com,REJECT
DOMAIN-SUFFIX,afd.l.google.com,REJECT
DOMAIN-SUFFIX,badad.googleplex.com,REJECT
DOMAIN-SUFFIX,cm.g.doubleclick.net,REJECT
DOMAIN-SUFFIX,doubleclick.com,REJECT
DOMAIN-SUFFIX,doubleclick.net,REJECT
DOMAIN-SUFFIX,googleadsserving.cn,REJECT
DOMAIN-SUFFIX,google-analytics.com,REJECT
DOMAIN-SUFFIX,googlecommerce.com,REJECT
DOMAIN-SUFFIX,googlesyndication.com,REJECT
DOMAIN-SUFFIX,googletagmanager.com,REJECT
DOMAIN-SUFFIX,googletagservices.com,REJECT
DOMAIN-SUFFIX,mm.admob.com,REJECT
DOMAIN-SUFFIX,mobileads.google.com,REJECT
DOMAIN-SUFFIX,pagead.google.com,REJECT
DOMAIN-SUFFIX,pagead.l.google.com,REJECT
DOMAIN-SUFFIX,pagead2.googlesyndication.com,REJECT
DOMAIN-SUFFIX,pagead-tpc.l.google.com,REJECT
DOMAIN-SUFFIX,partner.googleadservices.com,REJECT
DOMAIN-SUFFIX,pubads.g.doubleclick.net,REJECT
DOMAIN-SUFFIX,r.admob.com,REJECT
DOMAIN-SUFFIX,securepubads.g.doubleclick.net,REJECT
DOMAIN-SUFFIX,service.urchin.com,REJECT
DOMAIN-SUFFIX,static.googleadsserving.cn,REJECT
DOMAIN-SUFFIX,tpc.googlesyndication.com,REJECT
DOMAIN-SUFFIX,www.googleadservices.com,REJECT

// JD
DOMAIN-SUFFIX,ads.union.jd.com,REJECT
DOMAIN-SUFFIX,click.jr.jd.com,REJECT
DOMAIN-SUFFIX,c-nfa.jd.com,REJECT
DOMAIN-SUFFIX,cps.360buy.com,REJECT
DOMAIN-SUFFIX,du.jd.com,REJECT
DOMAIN-SUFFIX,jrclick.jd.com,REJECT
DOMAIN-SUFFIX,jzt.jd.com,REJECT
DOMAIN-SUFFIX,policy.jd.com,REJECT
DOMAIN-SUFFIX,stat.m.jd.com,REJECT
DOMAIN-SUFFIX,img-x.jd.com,REJECT

// Kugou
DOMAIN-SUFFIX,ads.service.kugou.com,REJECT
DOMAIN-SUFFIX,d.kugou.com,REJECT
DOMAIN-SUFFIX,downmobile.kugou.com,REJECT
DOMAIN-SUFFIX,fanxing.kugou.com,REJECT
DOMAIN-SUFFIX,gad.kugou.com,REJECT
DOMAIN-SUFFIX,game.kugou.com,REJECT
DOMAIN-SUFFIX,gcapi.sy.kugou.com,REJECT
DOMAIN-SUFFIX,gg.kugou.com,REJECT
DOMAIN-SUFFIX,install.kugou.com,REJECT
DOMAIN-SUFFIX,install2.kugou.com,REJECT
DOMAIN-SUFFIX,kgmobilestat.kugou.com,REJECT
DOMAIN-SUFFIX,kuaikaiapp.com,REJECT
DOMAIN-SUFFIX,log.stat.kugou.com,REJECT
DOMAIN-SUFFIX,minidcsc.kugou.com,REJECT
DOMAIN-SUFFIX,mo.kugou.com,REJECT
DOMAIN-SUFFIX,mobilelog.kugou.com,REJECT
DOMAIN-SUFFIX,msg.mobile.kugou.com,REJECT
DOMAIN-SUFFIX,mvads.kugou.com,REJECT
DOMAIN-SUFFIX,p.kugou.com,REJECT
DOMAIN-SUFFIX,push.mobile.kugou.com,REJECT
DOMAIN-SUFFIX,rtmonitor.kugou.com,REJECT
DOMAIN-SUFFIX,sdn.kugou.com,REJECT
DOMAIN-SUFFIX,tj.kugou.com,REJECT
DOMAIN-SUFFIX,update.mobile.kugou.com,REJECT

// Kuwo
DOMAIN-SUFFIX,apk.shouji.koowo.com,REJECT
DOMAIN-SUFFIX,deliver.kuwo.cn,REJECT
DOMAIN-SUFFIX,g.koowo.com,REJECT
DOMAIN-SUFFIX,g.kuwo.cn,REJECT
DOMAIN-SUFFIX,game.kuwo.cn,REJECT
DOMAIN-SUFFIX,kwmsg.kuwo.cn,REJECT
DOMAIN-SUFFIX,log.kuwo.cn,REJECT
DOMAIN-SUFFIX,mfan.iclick.com.cn,REJECT
DOMAIN-SUFFIX,mobilead.kuwo.cn,REJECT
DOMAIN-SUFFIX,msclick2.kuwo.cn,REJECT
DOMAIN-SUFFIX,msphoneclick.kuwo.cn,REJECT
DOMAIN-SUFFIX,updatepage.kuwo.cn,REJECT
DOMAIN-SUFFIX,wa.kuwo.cn,REJECT
DOMAIN-SUFFIX,webstat.kuwo.cn,REJECT

// Meitu
DOMAIN-SUFFIX,a.koudai.com,REJECT
DOMAIN-SUFFIX,corp.meitu.com,REJECT
DOMAIN-SUFFIX,gg.meitu.com,REJECT
DOMAIN-SUFFIX,meitubeauty.meitudata.com,REJECT
DOMAIN-SUFFIX,message.meitu.com,REJECT
DOMAIN-SUFFIX,tuiguang.meitu.com,REJECT
DOMAIN-SUFFIX,xiuxiu.android.dl.meitu.com,REJECT
DOMAIN-SUFFIX,xiuxiu.mobile.meitudata.com,REJECT

// Meizu
DOMAIN-SUFFIX,aider-res.meizu.com,REJECT
DOMAIN-SUFFIX,api-flow.flyme.cn,REJECT
DOMAIN-SUFFIX,api-game.meizu.com,REJECT
DOMAIN-SUFFIX,api-push.meizu.com,REJECT
DOMAIN-SUFFIX,aries.mzres.com,REJECT
DOMAIN-SUFFIX,bro.flyme.cn,REJECT
DOMAIN-SUFFIX,cal.meizu.com,REJECT
DOMAIN-SUFFIX,ebook.res.meizu.com,REJECT
DOMAIN-SUFFIX,game.res.meizu.com,REJECT
DOMAIN-SUFFIX,game-res.meizu.com,REJECT
DOMAIN-SUFFIX,infocenter.meizu.com,REJECT
DOMAIN-SUFFIX,openapi-news.meizu.com,REJECT
DOMAIN-SUFFIX,push.res.meizu.com,REJECT
DOMAIN-SUFFIX,reader.meizu.com,REJECT
DOMAIN-SUFFIX,reader.res.meizu.com,REJECT
DOMAIN-SUFFIX,t-e.flyme.cn,REJECT
DOMAIN-SUFFIX,t-flow.flyme.cn,REJECT
DOMAIN-SUFFIX,tongji.meizu.com,REJECT
DOMAIN-SUFFIX,tongji-res1.meizu.com,REJECT
DOMAIN-SUFFIX,umid.orion.meizu.com,REJECT
DOMAIN-SUFFIX,upush.res.meizu.com,REJECT
DOMAIN-SUFFIX,uxip.meizu.com,REJECT

// Moji
DOMAIN-SUFFIX,ad.api.moji.com,REJECT
DOMAIN-SUFFIX,app.moji001.com,REJECT
DOMAIN-SUFFIX,cdn.moji.com,REJECT
DOMAIN-SUFFIX,cdn.moji002.com,REJECT
DOMAIN-SUFFIX,cdn2.moji002.com,REJECT
DOMAIN-SUFFIX,fds.api.moji.com,REJECT
DOMAIN-SUFFIX,log.moji.com,REJECT
DOMAIN-SUFFIX,stat.moji.com,REJECT
DOMAIN-SUFFIX,ugc.moji001.com,REJECT

// Mop
DOMAIN-SUFFIX,pub.mop.com,REJECT

// Qingting.fm
DOMAIN-SUFFIX,ad.qingting.fm,REJECT
DOMAIN-SUFFIX,admgr.qingting.fm,REJECT
DOMAIN-SUFFIX,dload.qd.qingting.fm,REJECT
DOMAIN-SUFFIX,logger.qingting.fm,REJECT
DOMAIN-SUFFIX,s.qd.qingting.fm,REJECT
DOMAIN-SUFFIX,s.qd.qingtingfm.com,REJECT

// QQ
DOMAIN-SUFFIX,act.qq.com,REJECT
DOMAIN-SUFFIX,ad.qq.com,REJECT
DOMAIN-SUFFIX,ad.qun.qq.com,REJECT
DOMAIN-SUFFIX,adping.qq.com,REJECT
DOMAIN-SUFFIX,adpm.app.qq.com,REJECT
DOMAIN-SUFFIX,adrdir.qq.com,REJECT
DOMAIN-SUFFIX,adsclick.qq.com,REJECT
DOMAIN-SUFFIX,adsfile.qq.com,REJECT
DOMAIN-SUFFIX,adsgroup.qq.com,REJECT
DOMAIN-SUFFIX,adshmct.qq.com,REJECT
DOMAIN-SUFFIX,adshmmsg.qq.com,REJECT
DOMAIN-SUFFIX,adslvfile.qq.com,REJECT
DOMAIN-SUFFIX,adslvseed.qq.com,REJECT
DOMAIN-SUFFIX,adsqqclick.qq.com,REJECT
DOMAIN-SUFFIX,adstextview.qq.com,REJECT
DOMAIN-SUFFIX,adsview.qq.com,REJECT
DOMAIN-SUFFIX,adsview2.qq.com,REJECT
DOMAIN-SUFFIX,adv.app.qq.com,REJECT
DOMAIN-SUFFIX,adver.qq.com,REJECT
DOMAIN-SUFFIX,analy.qq.com,REJECT
DOMAIN-SUFFIX,boss.qzone.qq.com,REJECT
DOMAIN-SUFFIX,bugly.qq.com,REJECT
DOMAIN-SUFFIX,c.gdt.qq.com,REJECT
DOMAIN-SUFFIX,c2.l.qq.com,REJECT
DOMAIN-SUFFIX,ca.gtimg.com,REJECT
DOMAIN-SUFFIX,canvas.gdt.qq.com,REJECT
DOMAIN-SUFFIX,cb.l.qq.com,REJECT
DOMAIN-SUFFIX,d.gdt.qq.com,REJECT
DOMAIN-SUFFIX,d3g.qq.com,REJECT
DOMAIN-SUFFIX,e.qq.com,REJECT
DOMAIN-SUFFIX,etg.qq.com,REJECT
DOMAIN-SUFFIX,hm.l.qq.com,REJECT
DOMAIN-SUFFIX,i.gdt.qq.com,REJECT
DOMAIN-SUFFIX,ios.bugly.qq.com,REJECT
DOMAIN-SUFFIX,l2.l.qq.com,REJECT
DOMAIN-SUFFIX,lb.gtimg.com,REJECT
DOMAIN-SUFFIX,livem.l.qq.com,REJECT
DOMAIN-SUFFIX,ls.l.qq.com,REJECT
DOMAIN-SUFFIX,mini2015.qq.com,REJECT
DOMAIN-SUFFIX,p2.l.qq.com,REJECT
DOMAIN-SUFFIX,pgdt.gtimg.cn,REJECT
DOMAIN-SUFFIX,pingma.qq.com,REJECT
DOMAIN-SUFFIX,pingtcss.qq.com,REJECT
DOMAIN-SUFFIX,pms.mb.qq.com,REJECT
DOMAIN-SUFFIX,q.i.gdt.qq.com,REJECT
DOMAIN-SUFFIX,report.qq.com,REJECT
DOMAIN-SUFFIX,res.imtt.qq.com,REJECT
DOMAIN-SUFFIX,rm.gdt.qq.com,REJECT
DOMAIN-SUFFIX,tajs.qq.com,REJECT
DOMAIN-SUFFIX,tcss.qq.com,REJECT
DOMAIN-SUFFIX,updatecenter.qq.com,REJECT
DOMAIN-SUFFIX,uu.qq.com,REJECT
DOMAIN-SUFFIX,v.gdt.qq.com,REJECT
DOMAIN-SUFFIX,wb.gtimg.com,REJECT
DOMAIN-SUFFIX,win.gdt.qq.com,REJECT

// renren
DOMAIN-SUFFIX,ebp.renren.com,REJECT
DOMAIN-SUFFIX,jebe.renren.com,REJECT
DOMAIN-SUFFIX,jebe.xnimg.cn,REJECT

// Sina
DOMAIN-SUFFIX,ad.sina.com.cn,REJECT
DOMAIN-SUFFIX,adimg.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,adm.leju.sina.com.cn,REJECT
DOMAIN-SUFFIX,atm.sina.com,REJECT
DOMAIN-SUFFIX,c.biz.weibo.com,REJECT
DOMAIN-SUFFIX,c.wcpt.biz.weibo.com,REJECT
DOMAIN-SUFFIX,dcads.sina.com.cn,REJECT
DOMAIN-SUFFIX,game.weibo.cn,REJECT
DOMAIN-SUFFIX,game.weibo.com,REJECT
DOMAIN-SUFFIX,m.game.weibo.cn,REJECT
DOMAIN-SUFFIX,newspush.sinajs.cn,REJECT
DOMAIN-SUFFIX,ota.pay.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,pay.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,promote.biz.weibo.cn,REJECT
DOMAIN-SUFFIX,s.alitui.weibo.com,REJECT
DOMAIN-SUFFIX,sax.sina.cn,REJECT
DOMAIN-SUFFIX,sax.sina.com.cn,REJECT
DOMAIN-SUFFIX,sdkapp.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,sdkclick.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,tjs.sjs.sinajs.cn,REJECT
DOMAIN-SUFFIX,trends.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,wbapp.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,wbclick.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,wbpctips.mobile.sina.cn,REJECT
DOMAIN-SUFFIX,zc.biz.weibo.com,REJECT
DOMAIN-SUFFIX,zymo.mps.weibo.com,REJECT

// Sogou
DOMAIN-SUFFIX,123.sogou.com,REJECT
DOMAIN-SUFFIX,a1click.cpc.sogou.com,REJECT
DOMAIN-SUFFIX,adsence.sogou.com,REJECT
DOMAIN-SUFFIX,adstream.123.sogoucdn.com,REJECT
DOMAIN-SUFFIX,alpha.brand.sogou.com,REJECT
DOMAIN-SUFFIX,amfi.gou.sogou.com,REJECT
DOMAIN-SUFFIX,athena.wan.sogou.com,REJECT
DOMAIN-SUFFIX,bazinga.mse.sogou.com,REJECT
DOMAIN-SUFFIX,brand.sogou.com,REJECT
DOMAIN-SUFFIX,bsiet.husky.sogou.com,REJECT
DOMAIN-SUFFIX,cdn.optaim.com,REJECT
DOMAIN-SUFFIX,clk.optaim.com,REJECT
DOMAIN-SUFFIX,cm.optaim.com,REJECT
DOMAIN-SUFFIX,cpc.brand.sogou.com,REJECT
DOMAIN-SUFFIX,cpc.sogou.com,REJECT
DOMAIN-SUFFIX,demo.optaim.com,REJECT
DOMAIN-SUFFIX,dl.wan.sogoucdn.com,REJECT
DOMAIN-SUFFIX,fair.sogou.com,REJECT
DOMAIN-SUFFIX,files2.sogou.com,REJECT
DOMAIN-SUFFIX,galaxy.sogoucdn.com,REJECT
DOMAIN-SUFFIX,goto.sogou.com,REJECT
DOMAIN-SUFFIX,ht.www.sogou.com,REJECT
DOMAIN-SUFFIX,image.p4p.sogou.com,REJECT
DOMAIN-SUFFIX,img.wan.sogou.com,REJECT
DOMAIN-SUFFIX,imp.optaim.com,REJECT
DOMAIN-SUFFIX,inte.sogou.com,REJECT
DOMAIN-SUFFIX,irnvf.lu.sogou.com,REJECT
DOMAIN-SUFFIX,iwan.sogou.com,REJECT
DOMAIN-SUFFIX,kthxd.lu.sogou.com,REJECT
DOMAIN-SUFFIX,lk.brand.sogou.com,REJECT
DOMAIN-SUFFIX,lu.sogou.com,REJECT
DOMAIN-SUFFIX,lu.sogoucdn.com,REJECT
DOMAIN-SUFFIX,m.lu.sogou.com,REJECT
DOMAIN-SUFFIX,mini.cpc.sogou.com,REJECT
DOMAIN-SUFFIX,p.inte.sogou.com,REJECT
DOMAIN-SUFFIX,p.lu.sogou.com,REJECT
DOMAIN-SUFFIX,p3p.sogou.com,REJECT
DOMAIN-SUFFIX,pb.sogou.com,REJECT
DOMAIN-SUFFIX,pbd.sogou.com,REJECT
DOMAIN-SUFFIX,pv.focus.cn,REJECT
DOMAIN-SUFFIX,pv.sogou.com,REJECT
DOMAIN-SUFFIX,rd.e.sogou.com,REJECT
DOMAIN-SUFFIX,rjgw.theta.sogou.com,REJECT
DOMAIN-SUFFIX,sy.brand.sogou.com,REJECT
DOMAIN-SUFFIX,theta.sogoucdn.com,REJECT
DOMAIN-SUFFIX,tk.optaim.com,REJECT
DOMAIN-SUFFIX,vjoz.lu.sogou.com,REJECT
DOMAIN-SUFFIX,vps.inte.sogou.com,REJECT
DOMAIN-SUFFIX,wan.sogou.com,REJECT
DOMAIN-SUFFIX,wangmeng.sogou.com,REJECT
DOMAIN-SUFFIX,wb.brand.sogou.com,REJECT
DOMAIN-SUFFIX,web.sogou.com,REJECT
DOMAIN-SUFFIX,www.optaim.com,REJECT
DOMAIN-SUFFIX,ztrpm.lu.sogou.com,REJECT

// Tanx
DOMAIN-SUFFIX,a.tanx.com,REJECT
DOMAIN-SUFFIX,cdn.tanx.com,REJECT
DOMAIN-SUFFIX,cms.tanx.com,REJECT
DOMAIN-SUFFIX,df.tanx.com,REJECT
DOMAIN-SUFFIX,ecpm.tanx.com,REJECT
DOMAIN-SUFFIX,p.tanx.com,REJECT
DOMAIN-SUFFIX,pcookie.tanx.com,REJECT
DOMAIN-SUFFIX,phs.tanx.com,REJECT

// Teleplus
DOMAIN-SUFFIX,applovin.com,REJECT
DOMAIN-SUFFIX,guangzhuiyuan.com,REJECT

// Tianya
DOMAIN-SUFFIX,801.tianya.cn,REJECT
DOMAIN-SUFFIX,803.tianya.cn,REJECT
DOMAIN-SUFFIX,806.tianya.cn,REJECT
DOMAIN-SUFFIX,808.tianya.cn,REJECT
DOMAIN-SUFFIX,bdj.tianya.cn,REJECT
DOMAIN-SUFFIX,click.tianyaui.com,REJECT
DOMAIN-SUFFIX,dol.tianya.cn,REJECT

// Toutiao
DOMAIN-SUFFIX,ad.toutiao.com,REJECT
DOMAIN-SUFFIX,d.toutiao.com,REJECT
DOMAIN-SUFFIX,dm.toutiao.com,REJECT
DOMAIN-SUFFIX,dsp.toutiao.com,REJECT
DOMAIN-SUFFIX,nativeapp.toutiao.com,REJECT
DOMAIN-SUFFIX,partner.toutiao.com,REJECT

// Tumblr
DOMAIN-SUFFIX,92x.tumblr.com,REJECT
DOMAIN-SUFFIX,its-dori.tumblr.com,REJECT
DOMAIN-SUFFIX,tumblrprobes.cedexis.com,REJECT
DOMAIN-SUFFIX,tumblrreports.cedexis.com,REJECT

// Twitter
DOMAIN-SUFFIX,ads.twitter.com,REJECT
DOMAIN-SUFFIX,ads-twitter.com,REJECT
DOMAIN-SUFFIX,analytics.twitter.com,REJECT
DOMAIN-SUFFIX,p.twitter.com,REJECT
DOMAIN-SUFFIX,scribe.twitter.com,REJECT
DOMAIN-SUFFIX,syndication.twitter.com,REJECT
DOMAIN-SUFFIX,syndication-o.twitter.com,REJECT
DOMAIN-SUFFIX,urls.api.twitter.com,REJECT

// UC
DOMAIN-SUFFIX,adslot.uc.cn,REJECT
DOMAIN-SUFFIX,api.mp.uc.cn,REJECT
DOMAIN-SUFFIX,applog.uc.cn,REJECT
DOMAIN-SUFFIX,client.video.ucweb.com,REJECT
DOMAIN-SUFFIX,cms.ucweb.com,REJECT
DOMAIN-SUFFIX,dispatcher.upmc.uc.cn,REJECT
DOMAIN-SUFFIX,huichuan.sm.cn,REJECT
DOMAIN-SUFFIX,iflow.uczzd.com,REJECT
DOMAIN-SUFFIX,iflow.uczzd.com.cn,REJECT
DOMAIN-SUFFIX,iflow.uczzd.net,REJECT
DOMAIN-SUFFIX,log.cs.pp.cn,REJECT
DOMAIN-SUFFIX,m.uczzd.cn,REJECT
DOMAIN-SUFFIX,patriot.cs.pp.cn,REJECT
DOMAIN-SUFFIX,puds.ucweb.com,REJECT
DOMAIN-SUFFIX,server.m.pp.cn,REJECT
DOMAIN-SUFFIX,track.uc.cn,REJECT
DOMAIN-SUFFIX,u.uc123.com,REJECT
DOMAIN-SUFFIX,u.ucfly.com,REJECT
DOMAIN-SUFFIX,uc.ucweb.com,REJECT
DOMAIN-SUFFIX,ucsec.ucweb.com,REJECT
DOMAIN-SUFFIX,ucsec1.ucweb.com,REJECT

// Weifeng
DOMAIN-SUFFIX,aoodoo.feng.com,REJECT
DOMAIN-SUFFIX,bbsanalytics.weiphone.net,REJECT
DOMAIN-SUFFIX,fengbuy.com,REJECT
DOMAIN-SUFFIX,we.tm,REJECT

// Wi-Fi key
DOMAIN,wifiapi01.51y5.net,REJECT
DOMAIN,wifiapi02.51y5.net,REJECT
DOMAIN-SUFFIX,c.51y5.net,REJECT
DOMAIN-SUFFIX,cds.51y5.net,REJECT
DOMAIN-SUFFIX,ios-dc.51y5.net,REJECT
DOMAIN-SUFFIX,news-img.51y5.net,REJECT
DOMAIN-SUFFIX,wkanc.com,REJECT

// WPS
DOMAIN-SUFFIX,ios-informationplatform.wps.cn,REJECT
DOMAIN-SUFFIX,minfo.wps.cn,REJECT
DOMAIN-SUFFIX,mo.res.wpscdn.cn,REJECT

// Xiaomi
DOMAIN-SUFFIX,ad.xiaomi.com,REJECT
DOMAIN-SUFFIX,ad1.xiaomi.com,REJECT
DOMAIN-SUFFIX,adv.sec.miui.com,REJECT
DOMAIN-SUFFIX,api.tw06.xlmc.sec.miui.com,REJECT
DOMAIN-SUFFIX,beha.ksmobile.com,REJECT
DOMAIN-SUFFIX,bss.pandora.xiaomi.com,REJECT
DOMAIN-SUFFIX,cloudservice13.kingsoft-office-service.com,REJECT
DOMAIN-SUFFIX,counter.kingsoft.com,REJECT
DOMAIN-SUFFIX,de.pandora.xiaomi.com,REJECT
DOMAIN-SUFFIX,dvb.pandora.xiaomi.com,REJECT
DOMAIN-SUFFIX,etl.xlmc.sandai.net,REJECT
DOMAIN-SUFFIX,etl.xlmc.sec.miui.com,REJECT
DOMAIN-SUFFIX,event.ksosoft.com,REJECT
DOMAIN-SUFFIX,hot.browser.miui.com,REJECT
DOMAIN-SUFFIX,jellyfish.pandora.xiaomi.com,REJECT
DOMAIN-SUFFIX,mb.yidianzixun.com,REJECT
DOMAIN-SUFFIX,notice.game.xiaomi.com,REJECT
DOMAIN-SUFFIX,ppurifier.game.xiaomi.com,REJECT
DOMAIN-SUFFIX,r.browser.miui.com,REJECT
DOMAIN-SUFFIX,reader.browser.miui.com,REJECT
DOMAIN-SUFFIX,shenghuo.xiaomi.com,REJECT
DOMAIN-SUFFIX,stat.pandora.xiaomi.com,REJECT
DOMAIN-SUFFIX,tracking.miui.com,REJECT
DOMAIN-SUFFIX,uid.ksosoft.com,REJECT
DOMAIN-SUFFIX,ups.ksmobile.net,REJECT
DOMAIN-SUFFIX,ws.ksmobile.net,REJECT
DOMAIN-SUFFIX,wtradv.market.xiaomi.com,REJECT
DOMAIN-SUFFIX,xmpush.xiaomi.com,REJECT

// Ximalaya
DOMAIN-SUFFIX,ad.test.ximalaya.com,REJECT
DOMAIN-SUFFIX,ad.ximalaya.com,REJECT
DOMAIN-SUFFIX,adse.test.ximalaya.com,REJECT
DOMAIN-SUFFIX,adse.ximalaya.com,REJECT
DOMAIN-SUFFIX,adweb.test.ximalaya.com,REJECT
DOMAIN-SUFFIX,adweb.ximalaya.com,REJECT
DOMAIN-SUFFIX,linkeye.ximalaya.com,REJECT
DOMAIN-SUFFIX,location.ximalaya.com,REJECT
DOMAIN-SUFFIX,xdcs-collector.ximalaya.com,REJECT

// Zhangyue
DOMAIN-SUFFIX,m.ad.zhangyue.com,REJECT
DOMAIN-SUFFIX,sys.zhangyue.com,REJECT

// Zhihu
DOMAIN-SUFFIX,sugar.zhihu.com,REJECT
DOMAIN-SUFFIX,zhihu-analytics.zhihu.com,REJECT
DOMAIN-SUFFIX,zhihu-web-analytics.zhihu.com,REJECT
DOMAIN-SUFFIX,zhstatic.zhihu.com,REJECT

// AD Block
DOMAIN-KEYWORD,admarvel,REJECT
DOMAIN-KEYWORD,admdfs,REJECT
DOMAIN-KEYWORD,adnewnc,REJECT
DOMAIN-KEYWORD,adsage,REJECT
DOMAIN-KEYWORD,adsensor,REJECT
DOMAIN-KEYWORD,adsmogo,REJECT
DOMAIN-KEYWORD,adsrvmedia,REJECT
DOMAIN-KEYWORD,advert,REJECT
DOMAIN-KEYWORD,adwords,REJECT
DOMAIN-KEYWORD,analysis,REJECT
DOMAIN-KEYWORD,applovin,REJECT
DOMAIN-KEYWORD,dnserror,REJECT
DOMAIN-KEYWORD,domob,REJECT
DOMAIN-KEYWORD,duomeng,REJECT
DOMAIN-KEYWORD,dwtrack,REJECT
DOMAIN-KEYWORD,flurry.co,REJECT
DOMAIN-KEYWORD,guanggao,REJECT
DOMAIN-KEYWORD,inmobi,REJECT
DOMAIN-KEYWORD,lianmeng,REJECT
DOMAIN-KEYWORD,mobaders,REJECT
DOMAIN-KEYWORD,omgmta,REJECT
DOMAIN-KEYWORD,openx,REJECT
DOMAIN-KEYWORD,partnerad,REJECT
DOMAIN-KEYWORD,pingfore,REJECT
DOMAIN-KEYWORD,socdm,REJECT
DOMAIN-KEYWORD,supersonicads,REJECT
DOMAIN-KEYWORD,taboola,REJECT
DOMAIN-KEYWORD,uedas,REJECT
DOMAIN-KEYWORD,usage,REJECT
DOMAIN-KEYWORD,wlmonitor,REJECT
DOMAIN-KEYWORD,zjtoolbar,REJECT

DOMAIN-SUFFIX,000dn.com,REJECT
DOMAIN-SUFFIX,00880808.com,REJECT
DOMAIN-SUFFIX,00oo00.com,REJECT
DOMAIN-SUFFIX,01.gxso.net,REJECT
DOMAIN-SUFFIX,010teacher.com,REJECT
DOMAIN-SUFFIX,010xk.com,REJECT
DOMAIN-SUFFIX,022aifang.com,REJECT
DOMAIN-SUFFIX,023hysj.com,REJECT
DOMAIN-SUFFIX,025suyu.com,REJECT
DOMAIN-SUFFIX,0313413.com,REJECT
DOMAIN-SUFFIX,0451106.com,REJECT
DOMAIN-SUFFIX,0531kt.com,REJECT
DOMAIN-SUFFIX,0592weixin.com,REJECT
DOMAIN-SUFFIX,06362.com,REJECT
DOMAIN-SUFFIX,0756sjlm.com.cn,REJECT
DOMAIN-SUFFIX,0x01e7.website,REJECT
DOMAIN-SUFFIX,0xxd.com,REJECT
DOMAIN-SUFFIX,1.1010pic.com,REJECT
DOMAIN-SUFFIX,1.201980.com,REJECT
DOMAIN-SUFFIX,1.51sxue.cn,REJECT
DOMAIN-SUFFIX,1.cjcp.cn,REJECT
DOMAIN-SUFFIX,1.codesdq.com,REJECT
DOMAIN-SUFFIX,1.feihua.com,REJECT
DOMAIN-SUFFIX,1.glook.cn,REJECT
DOMAIN-SUFFIX,1.hao123.com,REJECT
DOMAIN-SUFFIX,1.hnyouneng.com,REJECT
DOMAIN-SUFFIX,1.i1766.com,REJECT
DOMAIN-SUFFIX,1.iqeq.com.cn,REJECT
DOMAIN-SUFFIX,1.jeasyui.net,REJECT
DOMAIN-SUFFIX,1.mgff.com,REJECT
DOMAIN-SUFFIX,1.panduoduo.net,REJECT
DOMAIN-SUFFIX,1.rengshu.com,REJECT
DOMAIN-SUFFIX,1.soufy.cn,REJECT
DOMAIN-SUFFIX,1.tulaoshi.com,REJECT
DOMAIN-SUFFIX,1.win7china.com,REJECT
DOMAIN-SUFFIX,1.win7sky.com,REJECT
DOMAIN-SUFFIX,1.xiaopin5.com,REJECT
DOMAIN-SUFFIX,1.xilu.com,REJECT
DOMAIN-SUFFIX,1.zw3e.com,REJECT
DOMAIN-SUFFIX,100.admin5.com,REJECT
DOMAIN-SUFFIX,1000dy.com,REJECT
DOMAIN-SUFFIX,100fenlm.com,REJECT
DOMAIN-SUFFIX,1017.cn,REJECT
DOMAIN-SUFFIX,10up.com,REJECT
DOMAIN-SUFFIX,11.hydcd.com,REJECT
DOMAIN-SUFFIX,111111qb.com,REJECT
DOMAIN-SUFFIX,111cn.net,REJECT
DOMAIN-SUFFIX,1133.cc,REJECT
DOMAIN-SUFFIX,114la.com,REJECT
DOMAIN-SUFFIX,114so.cn,REJECT
DOMAIN-SUFFIX,11g.yiqig.cn,REJECT
DOMAIN-SUFFIX,1224.dxsbb.com,REJECT
DOMAIN-SUFFIX,12306media.com,REJECT
DOMAIN-SUFFIX,1234xm.com,REJECT
DOMAIN-SUFFIX,12365chia.com,REJECT
DOMAIN-SUFFIX,123hala.com,REJECT
DOMAIN-SUFFIX,123juzi.net,REJECT
DOMAIN-SUFFIX,138138138.top,REJECT
DOMAIN-SUFFIX,1680go.com,REJECT
DOMAIN-SUFFIX,168ad.cc,REJECT
DOMAIN-SUFFIX,170yy.com,REJECT
DOMAIN-SUFFIX,175bar.com,REJECT
DOMAIN-SUFFIX,176um.com,REJECT
DOMAIN-SUFFIX,178gg.com,REJECT
DOMAIN-SUFFIX,17leyi.com,REJECT
DOMAIN-SUFFIX,17un.co,REJECT
DOMAIN-SUFFIX,18tzx.com,REJECT
DOMAIN-SUFFIX,1d1px.net,REJECT
DOMAIN-SUFFIX,1i580.com,REJECT
DOMAIN-SUFFIX,1kmb.cn,REJECT
DOMAIN-SUFFIX,1kzh.com,REJECT
DOMAIN-SUFFIX,1l1.cc,REJECT
DOMAIN-SUFFIX,1lib.cn,REJECT
DOMAIN-SUFFIX,1qwe3r.com,REJECT
DOMAIN-SUFFIX,1tlm.cn,REJECT
DOMAIN-SUFFIX,1uandun.com,REJECT
DOMAIN-SUFFIX,1x3x.com,REJECT
DOMAIN-SUFFIX,2.5aigushi.com,REJECT
DOMAIN-SUFFIX,2.heiyange.com,REJECT
DOMAIN-SUFFIX,2.mobixs.cn,REJECT
DOMAIN-SUFFIX,2.rengshu.com,REJECT
DOMAIN-SUFFIX,201071.com,REJECT
DOMAIN-SUFFIX,2012ui.com,REJECT
DOMAIN-SUFFIX,20150930.cf,REJECT
DOMAIN-SUFFIX,21union.com,REJECT
DOMAIN-SUFFIX,22.qingsongbar.com,REJECT
DOMAIN-SUFFIX,22lm.cc,REJECT
DOMAIN-SUFFIX,233wo.com,REJECT
DOMAIN-SUFFIX,24haitao.net,REJECT
DOMAIN-SUFFIX,268mob.cn,REJECT
DOMAIN-SUFFIX,272xb.com,REJECT
DOMAIN-SUFFIX,28acglz.com,REJECT
DOMAIN-SUFFIX,2a.com.cn,REJECT
DOMAIN-SUFFIX,2cnt.net,REJECT
DOMAIN-SUFFIX,2m2n.com,REJECT
DOMAIN-SUFFIX,3.guidaye.com,REJECT
DOMAIN-SUFFIX,3.ssqzj.com,REJECT
DOMAIN-SUFFIX,31.media.tumblr.com,REJECT
DOMAIN-SUFFIX,33.pcpop.com,REJECT
DOMAIN-SUFFIX,336.com,REJECT
DOMAIN-SUFFIX,339.cn,REJECT
DOMAIN-SUFFIX,3393.com,REJECT
DOMAIN-SUFFIX,33lm.cc,REJECT
DOMAIN-SUFFIX,33shangyou.com,REJECT
DOMAIN-SUFFIX,360640.com,REJECT
DOMAIN-SUFFIX,360baidus.com,REJECT
DOMAIN-SUFFIX,360safego.com,REJECT
DOMAIN-SUFFIX,361315.cc,REJECT
DOMAIN-SUFFIX,365bibi.com,REJECT
DOMAIN-SUFFIX,365safego.com,REJECT
DOMAIN-SUFFIX,366safego.com,REJECT
DOMAIN-SUFFIX,36pn.com,REJECT
DOMAIN-SUFFIX,376zf.com,REJECT
DOMAIN-SUFFIX,37cs.com,REJECT
DOMAIN-SUFFIX,37mnm.com,REJECT
DOMAIN-SUFFIX,37pk49.com,REJECT
DOMAIN-SUFFIX,37see.com,REJECT
DOMAIN-SUFFIX,3808010.com,REJECT
DOMAIN-SUFFIX,3839168.com,REJECT
DOMAIN-SUFFIX,38499.com,REJECT
DOMAIN-SUFFIX,3975lm.com,REJECT
DOMAIN-SUFFIX,39xc.net,REJECT
DOMAIN-SUFFIX,3dm.huya.com,REJECT
DOMAIN-SUFFIX,3dwwwgame.com,REJECT
DOMAIN-SUFFIX,3g.990.net,REJECT
DOMAIN-SUFFIX,3gmimo.com,REJECT
DOMAIN-SUFFIX,3gmtr.com,REJECT
DOMAIN-SUFFIX,3htai.com,REJECT
DOMAIN-SUFFIX,3qmh.com,REJECT
DOMAIN-SUFFIX,3ygww.com,REJECT
DOMAIN-SUFFIX,456juhd.com,REJECT
DOMAIN-SUFFIX,49wanwan.com,REJECT
DOMAIN-SUFFIX,4ggww.com,REJECT
DOMAIN-SUFFIX,4wad.com,REJECT
DOMAIN-SUFFIX,504pk.com,REJECT
DOMAIN-SUFFIX,51.la,REJECT
DOMAIN-SUFFIX,5125129.com,REJECT
DOMAIN-SUFFIX,513hch.com,REJECT
DOMAIN-SUFFIX,517m.cn,REJECT
DOMAIN-SUFFIX,51ads.com,REJECT
DOMAIN-SUFFIX,51gxqm.com,REJECT
DOMAIN-SUFFIX,51jumintong.com,REJECT
DOMAIN-SUFFIX,51weidashi.com,REJECT
DOMAIN-SUFFIX,51xumei.com,REJECT
DOMAIN-SUFFIX,51yes.com,REJECT
DOMAIN-SUFFIX,5207470.com,REJECT
DOMAIN-SUFFIX,5269120.com,REJECT
DOMAIN-SUFFIX,52kmh.com,REJECT
DOMAIN-SUFFIX,52kmk.com,REJECT
DOMAIN-SUFFIX,52lubo.cn,REJECT
DOMAIN-SUFFIX,559gp.com,REJECT
DOMAIN-SUFFIX,55lu.com,REJECT
DOMAIN-SUFFIX,57union.com,REJECT
DOMAIN-SUFFIX,592man.com,REJECT
DOMAIN-SUFFIX,5dian.org,REJECT
DOMAIN-SUFFIX,5egk.com,REJECT
DOMAIN-SUFFIX,5imoney.com,REJECT
DOMAIN-SUFFIX,5jcom.com.cn,REJECT
DOMAIN-SUFFIX,5y9nfpes.52pk.com,REJECT
DOMAIN-SUFFIX,600ad.com,REJECT
DOMAIN-SUFFIX,644446.com,REJECT
DOMAIN-SUFFIX,654mmm.com,REJECT
DOMAIN-SUFFIX,6669667.com,REJECT
DOMAIN-SUFFIX,66san.com,REJECT
DOMAIN-SUFFIX,6711.com,REJECT
DOMAIN-SUFFIX,6728812.com,REJECT
DOMAIN-SUFFIX,685wo.com,REJECT
DOMAIN-SUFFIX,68665565.com,REJECT
DOMAIN-SUFFIX,69duk.com,REJECT
DOMAIN-SUFFIX,6dad.com,REJECT
DOMAIN-SUFFIX,6dvip.com,REJECT
DOMAIN-SUFFIX,6huu.com,REJECT
DOMAIN-SUFFIX,70lm.com,REJECT
DOMAIN-SUFFIX,711kk.com,REJECT
DOMAIN-SUFFIX,71sem.com,REJECT
DOMAIN-SUFFIX,75to.com,REJECT
DOMAIN-SUFFIX,7631.com,REJECT
DOMAIN-SUFFIX,766ba.net,REJECT
DOMAIN-SUFFIX,778669.com,REJECT
DOMAIN-SUFFIX,7794.com,REJECT
DOMAIN-SUFFIX,77power.com,REJECT
DOMAIN-SUFFIX,77u.com,REJECT
DOMAIN-SUFFIX,77xtv.com,REJECT
DOMAIN-SUFFIX,7891655.cn,REJECT
DOMAIN-SUFFIX,7car.com.cn,REJECT
DOMAIN-SUFFIX,7dah8.com,REJECT
DOMAIN-SUFFIX,7jiajiao.com,REJECT
DOMAIN-SUFFIX,7mad.7m.cn,REJECT
DOMAIN-SUFFIX,7pk.com,REJECT
DOMAIN-SUFFIX,7wen.cn,REJECT
DOMAIN-SUFFIX,7xi9g1.com1.z0.glb.clouddn.com,REJECT
DOMAIN-SUFFIX,7xsf3h.com1.z0.glb.clouddn.com,REJECT
DOMAIN-SUFFIX,7xz3.com,REJECT
DOMAIN-SUFFIX,7z66.com,REJECT
DOMAIN-SUFFIX,801.tianyaui.com,REJECT
DOMAIN-SUFFIX,80sjw.com,REJECT
DOMAIN-SUFFIX,813690.top,REJECT
DOMAIN-SUFFIX,818mov.com,REJECT
DOMAIN-SUFFIX,81c.cn,REJECT
DOMAIN-SUFFIX,8368661.com,REJECT
DOMAIN-SUFFIX,8521448.com,REJECT
DOMAIN-SUFFIX,85655095.com,REJECT
DOMAIN-SUFFIX,859377.com,REJECT
DOMAIN-SUFFIX,85tgw.com,REJECT
DOMAIN-SUFFIX,86.cc,REJECT
DOMAIN-SUFFIX,860010.com,REJECT
DOMAIN-SUFFIX,86file.megajoy.com,REJECT
DOMAIN-SUFFIX,8800271.com.cn,REJECT
DOMAIN-SUFFIX,88210212.com,REJECT
DOMAIN-SUFFIX,8866786.com,REJECT
DOMAIN-SUFFIX,888.izhufu.net,REJECT
DOMAIN-SUFFIX,88818122.cn,REJECT
DOMAIN-SUFFIX,88rpg.net,REJECT
DOMAIN-SUFFIX,892155.com,REJECT
DOMAIN-SUFFIX,89h8.com,REJECT
DOMAIN-SUFFIX,8dulm.com,REJECT
DOMAIN-SUFFIX,8hykthze.cricket,REJECT
DOMAIN-SUFFIX,8jkx.com,REJECT
DOMAIN-SUFFIX,8le8le.com,REJECT
DOMAIN-SUFFIX,8mfty.com,REJECT
DOMAIN-SUFFIX,8ox.cn,REJECT
DOMAIN-SUFFIX,911.cc,REJECT
DOMAIN-SUFFIX,91ad.bestvogue.com,REJECT
DOMAIN-SUFFIX,91adv.com,REJECT
DOMAIN-SUFFIX,91hui.com,REJECT
DOMAIN-SUFFIX,91xry.com,REJECT
DOMAIN-SUFFIX,91ysa.com,REJECT
DOMAIN-SUFFIX,91zgm.com,REJECT
DOMAIN-SUFFIX,937744.com,REJECT
DOMAIN-SUFFIX,9377aa.com,REJECT
DOMAIN-SUFFIX,9377bh.com,REJECT
DOMAIN-SUFFIX,9377co.com,REJECT
DOMAIN-SUFFIX,9377hi.com,REJECT
DOMAIN-SUFFIX,9377if.com,REJECT
DOMAIN-SUFFIX,9377ku.com,REJECT
DOMAIN-SUFFIX,9377or.com,REJECT
DOMAIN-SUFFIX,9377os.com,REJECT
DOMAIN-SUFFIX,93manhua.com,REJECT
DOMAIN-SUFFIX,94lm.com,REJECT
DOMAIN-SUFFIX,95105012.com,REJECT
DOMAIN-SUFFIX,9519.net,REJECT
DOMAIN-SUFFIX,95558000.com,REJECT
DOMAIN-SUFFIX,96mob.com,REJECT
DOMAIN-SUFFIX,99909988.com,REJECT
DOMAIN-SUFFIX,99click.com,REJECT
DOMAIN-SUFFIX,99ddd.com,REJECT
DOMAIN-SUFFIX,99lolo.com,REJECT
DOMAIN-SUFFIX,9ads.net,REJECT
DOMAIN-SUFFIX,9dtiny.cn,REJECT
DOMAIN-SUFFIX,9kff.com,REJECT
DOMAIN-SUFFIX,9tn.cc,REJECT
DOMAIN-SUFFIX,9wushuo.com,REJECT
DOMAIN-SUFFIX,a.5ykj.com,REJECT
DOMAIN-SUFFIX,a.80982.org,REJECT
DOMAIN-SUFFIX,a.applovin.com,REJECT
DOMAIN-SUFFIX,a.armystar.com,REJECT
DOMAIN-SUFFIX,a.baiy.net,REJECT
DOMAIN-SUFFIX,a.baomihua.com,REJECT
DOMAIN-SUFFIX,a.cdngeek.net,REJECT
DOMAIN-SUFFIX,a.ckm.iqiyi.com,REJECT
DOMAIN-SUFFIX,a.clipconverter.cc,REJECT
DOMAIN-SUFFIX,a.duanmeiwen.com,REJECT
DOMAIN-SUFFIX,a.eporner.com,REJECT
DOMAIN-SUFFIX,a.exam58.com,REJECT
DOMAIN-SUFFIX,a.fengyx.com,REJECT
DOMAIN-SUFFIX,a.fwsir.com,REJECT
DOMAIN-SUFFIX,a.giantrealm.com,REJECT
DOMAIN-SUFFIX,a.kandiaoyu.com,REJECT
DOMAIN-SUFFIX,a.kejixun.com,REJECT
DOMAIN-SUFFIX,a.kickass.to,REJECT
DOMAIN-SUFFIX,a.livesportmedia.eu,REJECT
DOMAIN-SUFFIX,a.lolwot.com,REJECT
DOMAIN-SUFFIX,a.lz13.cn,REJECT
DOMAIN-SUFFIX,a.nanhuwang.com,REJECT
DOMAIN-SUFFIX,a.nowscore.com,REJECT
DOMAIN-SUFFIX,a.qinghua5.com,REJECT
DOMAIN-SUFFIX,a.shenchuang.com,REJECT
DOMAIN-SUFFIX,a.shuoshuodaquan.net,REJECT
DOMAIN-SUFFIX,a.solarmovie.is,REJECT
DOMAIN-SUFFIX,a.thefreethoughtproject.com,REJECT
DOMAIN-SUFFIX,a.ucoz.net,REJECT
DOMAIN-SUFFIX,a.visualrevenue.com,REJECT
DOMAIN-SUFFIX,a.xinwenge.net,REJECT
DOMAIN-SUFFIX,a.xixiyishu.com,REJECT
DOMAIN-SUFFIX,a.xizi.com,REJECT
DOMAIN-SUFFIX,a.xywy.com,REJECT
DOMAIN-SUFFIX,a.yangshengtang123.com,REJECT
DOMAIN-SUFFIX,a.yixie8.com,REJECT
DOMAIN-SUFFIX,a0b11.com,REJECT
DOMAIN-SUFFIX,a0b22.com,REJECT
DOMAIN-SUFFIX,a0b33.com,REJECT
DOMAIN-SUFFIX,a0c66.com,REJECT
DOMAIN-SUFFIX,a0c77.com,REJECT
DOMAIN-SUFFIX,a1.0s.net.cn,REJECT
DOMAIN-SUFFIX,a1.azg168.cn,REJECT
DOMAIN-SUFFIX,a1.gexing.me,REJECT
DOMAIN-SUFFIX,a1.huanqiumil.com,REJECT
DOMAIN-SUFFIX,a1.itc.cn,REJECT
DOMAIN-SUFFIX,a1.liuxue86.com,REJECT
DOMAIN-SUFFIX,a1.vdolady.com,REJECT
DOMAIN-SUFFIX,a1.yuuedu.com,REJECT
DOMAIN-SUFFIX,a2.b310.com,REJECT
DOMAIN-SUFFIX,a3p4.net,REJECT
DOMAIN-SUFFIX,a4.b2b168.com,REJECT
DOMAIN-SUFFIX,a4.yeshj.com,REJECT
DOMAIN-SUFFIX,a5.yeshj.com,REJECT
DOMAIN-SUFFIX,a7shun.com,REJECT
DOMAIN-SUFFIX,a907907.com,REJECT
DOMAIN-SUFFIX,a9377j.com,REJECT
DOMAIN-SUFFIX,aa.jiankang.com,REJECT
DOMAIN-SUFFIX,aa.tianya999.com,REJECT
DOMAIN-SUFFIX,aa.xiangxiangmf.com,REJECT
DOMAIN-SUFFIX,ab.sc115.com,REJECT
DOMAIN-SUFFIX,abc.hkepc.com,REJECT
DOMAIN-SUFFIX,ac2.msn.com,REJECT
DOMAIN-SUFFIX,acasys88.cn,REJECT
DOMAIN-SUFFIX,access.njherald.com,REJECT
DOMAIN-SUFFIX,acm.dzwww.com,REJECT
DOMAIN-SUFFIX,acs86.com,REJECT
DOMAIN-SUFFIX,acsystem.wasu.cn,REJECT
DOMAIN-SUFFIX,acwgf.com,REJECT
DOMAIN-SUFFIX,acxiom-online.com,REJECT
DOMAIN-SUFFIX,ad.17173.com,REJECT
DOMAIN-SUFFIX,ad.3.cn,REJECT
DOMAIN-SUFFIX,ad.360yield.com,REJECT
DOMAIN-SUFFIX,ad.95306.cn,REJECT
DOMAIN-SUFFIX,ad.about.co.kr,REJECT
DOMAIN-SUFFIX,ad.accessmediaproductions.com,REJECT
DOMAIN-SUFFIX,ad.api.3g.tudou.com,REJECT
DOMAIN-SUFFIX,ad.bitmedia.io,REJECT
DOMAIN-SUFFIX,ad.bjmama.net,REJECT
DOMAIN-SUFFIX,ad.cctv.com,REJECT
DOMAIN-SUFFIX,ad.cmvideo.cn,REJECT
DOMAIN-SUFFIX,ad.cooks.com,REJECT
DOMAIN-SUFFIX,ad.crichd.in,REJECT
DOMAIN-SUFFIX,ad.digitimes.com.tw,REJECT
DOMAIN-SUFFIX,ad.directmirror.com,REJECT
DOMAIN-SUFFIX,ad.download.cnet.com,REJECT
DOMAIN-SUFFIX,ad.duga.jp,REJECT
DOMAIN-SUFFIX,ad.eporner.com,REJECT
DOMAIN-SUFFIX,ad.evozi.com,REJECT
DOMAIN-SUFFIX,ad.flipboard.com,REJECT
DOMAIN-SUFFIX,ad.flux.com,REJECT
DOMAIN-SUFFIX,ad.fnnews.com,REJECT
DOMAIN-SUFFIX,ad.foxnetworks.com,REJECT
DOMAIN-SUFFIX,ad.icasthq.com,REJECT
DOMAIN-SUFFIX,ad.idgtn.net,REJECT
DOMAIN-SUFFIX,ad.iloveinterracial.com,REJECT
DOMAIN-SUFFIX,ad.jamba.net,REJECT
DOMAIN-SUFFIX,ad.jamster.com,REJECT
DOMAIN-SUFFIX,ad.jiemian.com,REJECT
DOMAIN-SUFFIX,ad.kissanime.io,REJECT
DOMAIN-SUFFIX,ad.kisscartoon.io,REJECT
DOMAIN-SUFFIX,ad.livere.co.kr,REJECT
DOMAIN-SUFFIX,ad.lyricswire.com,REJECT
DOMAIN-SUFFIX,ad.mail.ru,REJECT
DOMAIN-SUFFIX,ad.mangareader.net,REJECT
DOMAIN-SUFFIX,ad.mediabong.net,REJECT
DOMAIN-SUFFIX,ad.mesomorphosis.com,REJECT
DOMAIN-SUFFIX,ad.mi.com,REJECT
DOMAIN-SUFFIX,ad.newegg.com,REJECT
DOMAIN-SUFFIX,ad.openmultimedia.biz,REJECT
DOMAIN-SUFFIX,ad.outsidehub.com,REJECT
DOMAIN-SUFFIX,ad.pandora.tv,REJECT
DOMAIN-SUFFIX,ad.pickple.net,REJECT
DOMAIN-SUFFIX,ad.proxy.sh,REJECT
DOMAIN-SUFFIX,ad.r.worldssl.net,REJECT
DOMAIN-SUFFIX,ad.rambler.ru,REJECT
DOMAIN-SUFFIX,ad.reachlocal.com,REJECT
DOMAIN-SUFFIX,ad.reklamport.com,REJECT
DOMAIN-SUFFIX,ad.search.ch,REJECT
DOMAIN-SUFFIX,ad.sensismediasmart.com.au,REJECT
DOMAIN-SUFFIX,ad.services.distractify.com,REJECT
DOMAIN-SUFFIX,ad.slutload.com,REJECT
DOMAIN-SUFFIX,ad.smartclip.net,REJECT
DOMAIN-SUFFIX,ad.spielothek.so,REJECT
DOMAIN-SUFFIX,ad.spreaker.com,REJECT
DOMAIN-SUFFIX,ad.thepaper.cn,REJECT
DOMAIN-SUFFIX,ad.thisav.com,REJECT
DOMAIN-SUFFIX,ad.unimhk.com,REJECT
DOMAIN-SUFFIX,ad.userporn.com,REJECT
DOMAIN-SUFFIX,ad.vidaroo.com,REJECT
DOMAIN-SUFFIX,ad.walkgame.com,REJECT
DOMAIN-SUFFIX,ad.winningpartner.com,REJECT
DOMAIN-SUFFIX,ad.xmovies8.ru,REJECT
DOMAIN-SUFFIX,ad.yieldlab.net,REJECT
DOMAIN-SUFFIX,ad.zanox.com,REJECT
DOMAIN-SUFFIX,ad000000.com,REJECT
DOMAIN-SUFFIX,ad1.p5w.net,REJECT
DOMAIN-SUFFIX,ad4game.com,REJECT
DOMAIN-SUFFIX,ad7.com,REJECT
DOMAIN-SUFFIX,ad8.adfarm1.adition.com,REJECT
DOMAIN-SUFFIX,ad9377.com,REJECT
DOMAIN-SUFFIX,adadapted.com,REJECT
DOMAIN-SUFFIX,adadmin.house365.com,REJECT
DOMAIN-SUFFIX,adadvisor.net,REJECT
DOMAIN-SUFFIX,adap.tv,REJECT
DOMAIN-SUFFIX,adbana.com,REJECT
DOMAIN-SUFFIX,adbot.tw,REJECT
DOMAIN-SUFFIX,adbox.sina.com.cn,REJECT
DOMAIN-SUFFIX,ad-brix.com,REJECT
DOMAIN-SUFFIX,adbxb.com,REJECT
DOMAIN-SUFFIX,adcast.fblife.com,REJECT
DOMAIN-SUFFIX,adccoo.cn,REJECT
DOMAIN-SUFFIX,adchina.com,REJECT
DOMAIN-SUFFIX,adcitrus.com,REJECT
DOMAIN-SUFFIX,adcloud.jp,REJECT
DOMAIN-SUFFIX,adcolony.com,REJECT
DOMAIN-SUFFIX,adcome.cn,REJECT
DOMAIN-SUFFIX,add.bugun.com.tr,REJECT
DOMAIN-SUFFIX,add.dz19.net,REJECT
DOMAIN-SUFFIX,add.freeimg8.com,REJECT
DOMAIN-SUFFIX,add.mmyuer.com,REJECT
DOMAIN-SUFFIX,addirector.vindicosuite.com,REJECT
DOMAIN-SUFFIX,adds.weatherology.com,REJECT
DOMAIN-SUFFIX,ader.mobi,REJECT
DOMAIN-SUFFIX,adexprt.com,REJECT
DOMAIN-SUFFIX,adf.dahe.cn,REJECT
DOMAIN-SUFFIX,adform.net,REJECT
DOMAIN-SUFFIX,adfurikun.jp,REJECT
DOMAIN-SUFFIX,adfuture.cn,REJECT
DOMAIN-SUFFIX,adhai.com,REJECT
DOMAIN-SUFFIX,adhome.1fangchan.com,REJECT
DOMAIN-SUFFIX,adhouyi.com,REJECT
DOMAIN-SUFFIX,adinall.com,REJECT
DOMAIN-SUFFIX,adinfuse.com,REJECT
DOMAIN-SUFFIX,adingo.jp.eimg.jp,REJECT
DOMAIN-SUFFIX,adirects.com,REJECT
DOMAIN-SUFFIX,adjb.5nd.com,REJECT
DOMAIN-SUFFIX,adjust.com,REJECT
DOMAIN-SUFFIX,adjust.io,REJECT
DOMAIN-SUFFIX,adk.funshion.com,REJECT
DOMAIN-SUFFIX,adk2x.com,REJECT
DOMAIN-SUFFIX,adkmob.com,REJECT
DOMAIN-SUFFIX,adkongjian.com,REJECT
DOMAIN-SUFFIX,adlink.shopsafe.co.nz,REJECT
DOMAIN-SUFFIX,adlive.cn,REJECT
DOMAIN-SUFFIX,adlocus.com,REJECT
DOMAIN-SUFFIX,ad-locus.com,REJECT
DOMAIN-SUFFIX,adm.265g.com,REJECT
DOMAIN-SUFFIX,adm.72zx.com,REJECT
DOMAIN-SUFFIX,adm.86wan.com,REJECT
DOMAIN-SUFFIX,adm.cloud.cnfol.com,REJECT
DOMAIN-SUFFIX,adm.funshion.com,REJECT
DOMAIN-SUFFIX,adm.xmfish.com,REJECT
DOMAIN-SUFFIX,adm.zbinfo.net,REJECT
DOMAIN-SUFFIX,admaji.com,REJECT
DOMAIN-SUFFIX,admarvel.com,REJECT
DOMAIN-SUFFIX,admaster.com.cn,REJECT
DOMAIN-SUFFIX,admd.yam.com,REJECT
DOMAIN-SUFFIX,admedia.com,REJECT
DOMAIN-SUFFIX,admeta.vo.llnwd.net,REJECT
DOMAIN-SUFFIX,admin5.com,REJECT
DOMAIN-SUFFIX,admin6.com,REJECT
DOMAIN-SUFFIX,admob.com,REJECT
DOMAIN-SUFFIX,admon.cn,REJECT
DOMAIN-SUFFIX,admtpmp124.com,REJECT
DOMAIN-SUFFIX,admx.baixing.com,REJECT
DOMAIN-SUFFIX,adn.ebay.com,REJECT
DOMAIN-SUFFIX,adnetpub.yaolan.com,REJECT
DOMAIN-SUFFIX,adnxs.com,REJECT
DOMAIN-SUFFIX,adomv.com,REJECT
DOMAIN-SUFFIX,adp.cnool.net,REJECT
DOMAIN-SUFFIX,ad-plus.cn,REJECT
DOMAIN-SUFFIX,adplus.goo.mx,REJECT
DOMAIN-SUFFIX,adpolestar.net,REJECT
DOMAIN-SUFFIX,adpro.cn,REJECT
DOMAIN-SUFFIX,adpub.yaolan.com,REJECT
DOMAIN-SUFFIX,adpubs.yaolan.com,REJECT
DOMAIN-SUFFIX,adpush.cn,REJECT
DOMAIN-SUFFIX,adquan.com,REJECT
DOMAIN-SUFFIX,adreal.cn,REJECT
DOMAIN-SUFFIX,adriver.ru,REJECT
DOMAIN-SUFFIX,adroll.com,REJECT
DOMAIN-SUFFIX,adrotator.se,REJECT
DOMAIN-SUFFIX,adrs.sdo.com,REJECT
DOMAIN-SUFFIX,ads.feedly.com,REJECT
DOMAIN-SUFFIX,ads.genieessp.com,REJECT
DOMAIN-SUFFIX,ads.heyzap.com,REJECT
DOMAIN-SUFFIX,ads.mobclix.com,REJECT
DOMAIN-SUFFIX,ads.mopub.com,REJECT
DOMAIN-SUFFIX,ads.mp.mydas.mobi,REJECT
DOMAIN-SUFFIX,ads.newtentionassets.net,REJECT
DOMAIN-SUFFIX,ads.nexage.com,REJECT
DOMAIN-SUFFIX,ads.oneway.mobi,REJECT
DOMAIN-SUFFIX,ads.pof.com,REJECT
DOMAIN-SUFFIX,ads.stickyadstv.com,REJECT
DOMAIN-SUFFIX,ads.tremorhub.com,REJECT
DOMAIN-SUFFIX,ads.uc.cn,REJECT
DOMAIN-SUFFIX,ads.videosz.com,REJECT
DOMAIN-SUFFIX,ads.xxxbunker.com,REJECT
DOMAIN-SUFFIX,ads.yahoo.com,REJECT
DOMAIN-SUFFIX,ads.zynga.com,REJECT
DOMAIN-SUFFIX,ads360.cn,REJECT
DOMAIN-SUFFIX,ads8.com,REJECT
DOMAIN-SUFFIX,ads80.com,REJECT
DOMAIN-SUFFIX,adsage.cn,REJECT
DOMAIN-SUFFIX,adsage.com,REJECT
DOMAIN-SUFFIX,adsame.com,REJECT
DOMAIN-SUFFIX,adsame1.cnr.cn,REJECT
DOMAIN-SUFFIX,adsatt.abcnews.starwave.com,REJECT
DOMAIN-SUFFIX,adsatt.espn.starwave.com,REJECT
DOMAIN-SUFFIX,adscaspion.appspot.com,REJECT
DOMAIN-SUFFIX,adsclick.yx.js.cn,REJECT
DOMAIN-SUFFIX,adserve2.tom.com,REJECT
DOMAIN-SUFFIX,adserver.snapads.com,REJECT
DOMAIN-SUFFIX,adshare.freedocast.com,REJECT
DOMAIN-SUFFIX,adshost2.com,REJECT
DOMAIN-SUFFIX,adshows.21cn.com,REJECT
DOMAIN-SUFFIX,adsinstant.com,REJECT
DOMAIN-SUFFIX,adsmogo.com,REJECT
DOMAIN-SUFFIX,adsmogo.org,REJECT
DOMAIN-SUFFIX,adsor.openrunner.com,REJECT
DOMAIN-SUFFIX,adsp.xunlei.com,REJECT
DOMAIN-SUFFIX,adsrich.qq.com,REJECT
DOMAIN-SUFFIX,adss.dotdo.net,REJECT
DOMAIN-SUFFIX,adss.yahoo.com,REJECT
DOMAIN-SUFFIX,adstil.indiatimes.com,REJECT
DOMAIN-SUFFIX,ad-stir.com,REJECT
DOMAIN-SUFFIX,adsunflower.com,REJECT
DOMAIN-SUFFIX,adsunion.com,REJECT
DOMAIN-SUFFIX,adsymptotic.com,REJECT
DOMAIN-SUFFIX,adtest.theonion.com,REJECT
DOMAIN-SUFFIX,adtrk.me,REJECT
DOMAIN-SUFFIX,adultfriendfinder.com,REJECT
DOMAIN-SUFFIX,adups.com,REJECT
DOMAIN-SUFFIX,aduu.cn,REJECT
DOMAIN-SUFFIX,adv.ccb.com,REJECT
DOMAIN-SUFFIX,advert.api.thejoyrun.com,REJECT
DOMAIN-SUFFIX,advertise.twitpic.com,REJECT
DOMAIN-SUFFIX,advertising.com,REJECT
DOMAIN-SUFFIX,adview.cn,REJECT
DOMAIN-SUFFIX,advmob.cn,REJECT
DOMAIN-SUFFIX,adwhirl.com,REJECT
DOMAIN-SUFFIX,adwo.com,REJECT
DOMAIN-SUFFIX,adx.kat.ph,REJECT
DOMAIN-SUFFIX,adxmi.com,REJECT
DOMAIN-SUFFIX,adxpansion.com,REJECT
DOMAIN-SUFFIX,adytx.com,REJECT
DOMAIN-SUFFIX,adyun.com,REJECT
DOMAIN-SUFFIX,adz.zwee.ly,REJECT
DOMAIN-SUFFIX,adzerk.net,REJECT
DOMAIN-SUFFIX,aercxy.com,REJECT
DOMAIN-SUFFIX,aff.eteachergroup.com,REJECT
DOMAIN-SUFFIX,aff.lmgtfy.com,REJECT
DOMAIN-SUFFIX,aff.marathonbet.com,REJECT
DOMAIN-SUFFIX,aff.svjump.com,REJECT
DOMAIN-SUFFIX,affil.mupromo.com,REJECT
DOMAIN-SUFFIX,affiliateprogram.keywordspy.com,REJECT
DOMAIN-SUFFIX,affiliates.allposters.com,REJECT
DOMAIN-SUFFIX,affiliates.goodvibes.com,REJECT
DOMAIN-SUFFIX,affiliates.thrixxx.com,REJECT
DOMAIN-SUFFIX,affiliatesmedia.sbobet.com,REJECT
DOMAIN-SUFFIX,affiliation.fotovista.com,REJECT
DOMAIN-SUFFIX,afjlb.com,REJECT
DOMAIN-SUFFIX,afp.chinanews.com,REJECT
DOMAIN-SUFFIX,afp.m1905.com,REJECT
DOMAIN-SUFFIX,afp.wasu.cn,REJECT
DOMAIN-SUFFIX,afpcreative.wasu.cn,REJECT
DOMAIN-SUFFIX,afpimages.eastday,REJECT
DOMAIN-SUFFIX,agenda.complex.com,REJECT
DOMAIN-SUFFIX,agrantsem.com,REJECT
DOMAIN-SUFFIX,ahhuazhen.com,REJECT
DOMAIN-SUFFIX,ahyau.com,REJECT
DOMAIN-SUFFIX,ahyuns.com,REJECT
DOMAIN-SUFFIX,ai.bioon.com,REJECT
DOMAIN-SUFFIX,aicydb.com,REJECT
DOMAIN-SUFFIX,aid.chinayk.com,REJECT
DOMAIN-SUFFIX,aikan6.com,REJECT
DOMAIN-SUFFIX,airpushmarketing.s3.amazonaws.com,REJECT
DOMAIN-SUFFIX,ais.abacast.com,REJECT
DOMAIN-SUFFIX,aishang.bid,REJECT
DOMAIN-SUFFIX,aishiguolong.com,REJECT
DOMAIN-SUFFIX,aiwen.cc,REJECT
DOMAIN-SUFFIX,ajapk.com,REJECT
DOMAIN-SUFFIX,ajaxcdn.org,REJECT
DOMAIN-SUFFIX,ajhdf.com,REJECT
DOMAIN-SUFFIX,ajnad.aljazeera.net,REJECT
DOMAIN-SUFFIX,ajuhd.com,REJECT
DOMAIN-SUFFIX,ak.sascdn.com,REJECT
DOMAIN-SUFFIX,akrwi.cn,REJECT
DOMAIN-SUFFIX,alicmayuns.com,REJECT
DOMAIN-SUFFIX,alimama.alicdn.com,REJECT
DOMAIN-SUFFIX,aliqqjd.cn,REJECT
DOMAIN-SUFFIX,alisinak.com,REJECT
DOMAIN-SUFFIX,alitianxia168.com,REJECT
DOMAIN-SUFFIX,alitui.weibo.com,REJECT
DOMAIN-SUFFIX,aliyuncss.com,REJECT
DOMAIN-SUFFIX,aliyunxin.com,REJECT
DOMAIN-SUFFIX,allxin.com,REJECT
DOMAIN-SUFFIX,allyes.cn,REJECT
DOMAIN-SUFFIX,alvares.esportsheaven.com,REJECT
DOMAIN-SUFFIX,am.6park.com,REJECT
DOMAIN-SUFFIX,am.szhome.com,REJECT
DOMAIN-SUFFIX,am15.net,REJECT
DOMAIN-SUFFIX,amazingmagics.com,REJECT
DOMAIN-SUFFIX,amazon-adsystem.com,REJECT
DOMAIN-SUFFIX,ams.fx678.com,REJECT
DOMAIN-SUFFIX,amz.steamprices.com,REJECT
DOMAIN-SUFFIX,analysys.cn,REJECT
DOMAIN-SUFFIX,analytics.126.net,DIRECT
DOMAIN-SUFFIX,analytics.disneyinternational.com,REJECT
DOMAIN-SUFFIX,analytics.mmosite.com,REJECT
DOMAIN-SUFFIX,analytics.query.yahoo.com,REJECT
DOMAIN-SUFFIX,andmejs.com,REJECT
DOMAIN-SUFFIX,angsrvr.com,REJECT
DOMAIN-SUFFIX,anioscp.com,REJECT
DOMAIN-SUFFIX,ann5.net,REJECT
DOMAIN-SUFFIX,anquan.org,REJECT
DOMAIN-SUFFIX,anreson.net,REJECT
DOMAIN-SUFFIX,anysdk.com,REJECT
DOMAIN-SUFFIX,aoodoo.weiphone.com,REJECT
DOMAIN-SUFFIX,api.mobula.sdk.duapps.com,REJECT
DOMAIN-SUFFIX,api.similarweb.com,REJECT
DOMAIN-SUFFIX,api.talkingdata.com,REJECT
DOMAIN-SUFFIX,api.userstyles.org,REJECT
DOMAIN-SUFFIX,apkdo.com,REJECT
DOMAIN-SUFFIX,appads.com,REJECT
DOMAIN-SUFFIX,appboy.com,REJECT
DOMAIN-SUFFIX,appdriver.cn,REJECT
DOMAIN-SUFFIX,apple.www.letv.com,REJECT
DOMAIN-SUFFIX,applifier.com,REJECT
DOMAIN-SUFFIX,app-measurement.com,REJECT
DOMAIN-SUFFIX,appservices.comcsoft.com,REJECT
DOMAIN-SUFFIX,aqgyju.cn,REJECT
DOMAIN-SUFFIX,aralego.com,REJECT
DOMAIN-SUFFIX,ard.ihookup.com,REJECT
DOMAIN-SUFFIX,ard.sweetdiscreet.com,REJECT
DOMAIN-SUFFIX,arealx.com,REJECT
DOMAIN-SUFFIX,as.inbox.com,REJECT
DOMAIN-SUFFIX,as.sinahk.net,REJECT
DOMAIN-SUFFIX,asd.projectfreetv.so,REJECT
DOMAIN-SUFFIX,ashow.pcpop.com,REJECT
DOMAIN-SUFFIX,aswlx.cn,REJECT
DOMAIN-SUFFIX,at98.com,REJECT
DOMAIN-SUFFIX,atdmt.com,REJECT
DOMAIN-SUFFIX,atiws.aipai.com,REJECT
DOMAIN-SUFFIX,audience.network,REJECT
DOMAIN-SUFFIX,avpa.dzone.com,REJECT
DOMAIN-SUFFIX,award.sitekeuring.net,REJECT
DOMAIN-SUFFIX,awkjs.com,REJECT
DOMAIN-SUFFIX,awyys.com,REJECT
DOMAIN-SUFFIX,axhxa.com,REJECT
DOMAIN-SUFFIX,axiba66.com,REJECT
DOMAIN-SUFFIX,axkxy.com,REJECT
DOMAIN-SUFFIX,b.77vcd.com,REJECT
DOMAIN-SUFFIX,b.babylon.com,REJECT
DOMAIN-SUFFIX,b.baiy.net,REJECT
DOMAIN-SUFFIX,b.cyone.com.cn,REJECT
DOMAIN-SUFFIX,b.livesport.eu,REJECT
DOMAIN-SUFFIX,b.localpages.com,REJECT
DOMAIN-SUFFIX,b.thefile.me,REJECT
DOMAIN-SUFFIX,b.xcafe.com,REJECT
DOMAIN-SUFFIX,b1.51scw.net,REJECT
DOMAIN-SUFFIX,b1.91jucai.com,REJECT
DOMAIN-SUFFIX,b1.c1km4.com,REJECT
DOMAIN-SUFFIX,b17.8794.cn,REJECT
DOMAIN-SUFFIX,b17.shangc.net,REJECT
DOMAIN-SUFFIX,b7nkd.cn,REJECT
DOMAIN-SUFFIX,b92.putniktravel.com,REJECT
DOMAIN-SUFFIX,b9377h.com,REJECT
DOMAIN-SUFFIX,b99u.top,REJECT
DOMAIN-SUFFIX,ba.ccm2.net,REJECT
DOMAIN-SUFFIX,ba.kioskea.net,REJECT
DOMAIN-SUFFIX,badao37.net,REJECT
DOMAIN-SUFFIX,bai3.gushiwen.org,REJECT
DOMAIN-SUFFIX,baiapk.com,REJECT
DOMAIN-SUFFIX,baiduace.com,REJECT
DOMAIN-SUFFIX,baidujs.cnys.com,REJECT
DOMAIN-SUFFIX,baidulao.com,REJECT
DOMAIN-SUFFIX,baifen.music.baidu.com,REJECT
DOMAIN-SUFFIX,baifendian.com,REJECT
DOMAIN-SUFFIX,bam.nr-data.net,REJECT
DOMAIN-SUFFIX,banmamedia.com,REJECT
DOMAIN-SUFFIX,banner.101xp.com,REJECT
DOMAIN-SUFFIX,banner.3ddownloads.com,REJECT
DOMAIN-SUFFIX,banner.automotiveworld.com,REJECT
DOMAIN-SUFFIX,banner.europacasino.com,REJECT
DOMAIN-SUFFIX,banner.itweb.co.za,REJECT
DOMAIN-SUFFIX,banner.telefragged.com,REJECT
DOMAIN-SUFFIX,banner.titancasino.com,REJECT
DOMAIN-SUFFIX,banner1.pornhost.com,REJECT
DOMAIN-SUFFIX,banners.beevpn.com,REJECT
DOMAIN-SUFFIX,banners.beted.com,REJECT
DOMAIN-SUFFIX,banners.cams.com,REJECT
DOMAIN-SUFFIX,banners.clubworldgroup.com,REJECT
DOMAIN-SUFFIX,banners.expressindia.com,REJECT
DOMAIN-SUFFIX,banners.itweb.co.za,REJECT
DOMAIN-SUFFIX,banners.playocio.com,REJECT
DOMAIN-SUFFIX,bannershotlink.perfectgonzo.com,REJECT
DOMAIN-SUFFIX,baoyatu.cc,REJECT
DOMAIN-SUFFIX,base.filedot.xyz,REJECT
DOMAIN-SUFFIX,bat.bing.com,REJECT
DOMAIN-SUFFIX,baycode.cn,REJECT
DOMAIN-SUFFIX,bb.tuku.cc,REJECT
DOMAIN-SUFFIX,bbdm.051661.com,REJECT
DOMAIN-SUFFIX,bccyyc.com,REJECT
DOMAIN-SUFFIX,bd.ershenghuo.com,REJECT
DOMAIN-SUFFIX,bd.haomagujia.com,REJECT
DOMAIN-SUFFIX,bd.wayqq.cn,REJECT
DOMAIN-SUFFIX,bd01.daqiso.com,REJECT
DOMAIN-SUFFIX,bd1.365qilu.com,REJECT
DOMAIN-SUFFIX,bd1.fengdu100.com,REJECT
DOMAIN-SUFFIX,bd1.jobui.com,REJECT
DOMAIN-SUFFIX,bd1.nipic.com,REJECT
DOMAIN-SUFFIX,bd1.nxing.cn,REJECT
DOMAIN-SUFFIX,bd1.szhk.com,REJECT
DOMAIN-SUFFIX,bd1.wowoqq.com,REJECT
DOMAIN-SUFFIX,bd1.xiangha.com,REJECT
DOMAIN-SUFFIX,bdad.hao224.com,REJECT
DOMAIN-SUFFIX,bdcode.gaosan.com,REJECT
DOMAIN-SUFFIX,bdcode.youke.com,REJECT
DOMAIN-SUFFIX,bdjiaoben.wmxa.cn,REJECT
DOMAIN-SUFFIX,bdjs.120askimages.com,REJECT
DOMAIN-SUFFIX,bdjs.99.com.cn,REJECT
DOMAIN-SUFFIX,bdjs.faxingzhan.com,REJECT
DOMAIN-SUFFIX,bdjs.ixiumei.com,REJECT
DOMAIN-SUFFIX,bdjs.kaixin100.com,REJECT
DOMAIN-SUFFIX,bdjs.ylq.com,REJECT
DOMAIN-SUFFIX,bdlm1.hc360.com,REJECT
DOMAIN-SUFFIX,bdlncs1.familydoctor.com.cn,REJECT
DOMAIN-SUFFIX,bdpuaw.com,REJECT
DOMAIN-SUFFIX,bdtongfei.cn,REJECT
DOMAIN-SUFFIX,beacon.krxd.net,REJECT
DOMAIN-SUFFIX,beacon.sina.com.cn,REJECT
DOMAIN-SUFFIX,beacon.tingyun.com,REJECT
DOMAIN-SUFFIX,beap.gemini.yahoo.com,REJECT
DOMAIN-SUFFIX,bebelait.com,REJECT
DOMAIN-SUFFIX,becode.qiushibaike.com,REJECT
DOMAIN-SUFFIX,beeho.site,REJECT
DOMAIN-SUFFIX,behe.com,REJECT
DOMAIN-SUFFIX,bepolite.eu,REJECT
DOMAIN-SUFFIX,besc.baidustatic.com,REJECT
DOMAIN-SUFFIX,bfdcdn.com,REJECT
DOMAIN-SUFFIX,biddingos.com,REJECT
DOMAIN-SUFFIX,biddingx.com,REJECT
DOMAIN-SUFFIX,bigbos.top,REJECT
DOMAIN-SUFFIX,bigboy.eurogamer.net,REJECT
DOMAIN-SUFFIX,billionfocus.com,REJECT
DOMAIN-SUFFIX,bingyinq.com,REJECT
DOMAIN-SUFFIX,bivitr.com,REJECT
DOMAIN-SUFFIX,biyibia.com,REJECT
DOMAIN-SUFFIX,biz.gexing.com,REJECT
DOMAIN-SUFFIX,biz.weibo.com,REJECT
DOMAIN-SUFFIX,biz37.net,REJECT
DOMAIN-SUFFIX,bizanti.youwatch.org,REJECT
DOMAIN-SUFFIX,bjcathay.com,REJECT
DOMAIN-SUFFIX,bjs.9669.cn,REJECT
DOMAIN-SUFFIX,bl.wavecdn.de,REJECT
DOMAIN-SUFFIX,blaaaa12.googlecode.com,REJECT
DOMAIN-SUFFIX,bloggerads.net,REJECT
DOMAIN-SUFFIX,bluhostedbanners.blucigs.com,REJECT
DOMAIN-SUFFIX,bnrs.ilm.ee,REJECT
DOMAIN-SUFFIX,bob.crazyshit.com,REJECT
DOMAIN-SUFFIX,bosiwangzi.cn,REJECT
DOMAIN-SUFFIX,box.anchorfree.net,REJECT
DOMAIN-SUFFIX,boyxu.cn,REJECT
DOMAIN-SUFFIX,br.blackfling.com,REJECT
DOMAIN-SUFFIX,br.fling.com,REJECT
DOMAIN-SUFFIX,br.realitykings.com,REJECT
DOMAIN-SUFFIX,brcache.madthumbs.com,REJECT
DOMAIN-SUFFIX,breezily168.com,REJECT
DOMAIN-SUFFIX,bsdev.cn,REJECT
DOMAIN-SUFFIX,bstatic.1kejian.com,REJECT
DOMAIN-SUFFIX,bstatic.diyifanwen.com,REJECT
DOMAIN-SUFFIX,btn.onlylady.com,REJECT
DOMAIN-SUFFIX,btn.pchome.net,REJECT
DOMAIN-SUFFIX,btr.domywife.com,REJECT
DOMAIN-SUFFIX,btyou.com,REJECT
DOMAIN-SUFFIX,bugtags.com,REJECT
DOMAIN-SUFFIX,business.92wy.com,REJECT
DOMAIN-SUFFIX,buysellads.com,REJECT
DOMAIN-SUFFIX,bwp.theinsider.com.com,REJECT
DOMAIN-SUFFIX,bxgmb.com,REJECT
DOMAIN-SUFFIX,bxjpl.cn,REJECT
DOMAIN-SUFFIX,by.dm5.com,REJECT
DOMAIN-SUFFIX,by.tel.cdndm.com,REJECT
DOMAIN-SUFFIX,by8974.com,REJECT
DOMAIN-SUFFIX,bydonline.com,REJECT
DOMAIN-SUFFIX,bypbwm.cn,REJECT
DOMAIN-SUFFIX,c.35kds.com,REJECT
DOMAIN-SUFFIX,c.metrigo.com,REJECT
DOMAIN-SUFFIX,c.netu.tv,REJECT
DOMAIN-SUFFIX,c0563.com,REJECT
DOMAIN-SUFFIX,c1.4qx.net,REJECT
DOMAIN-SUFFIX,caamei.com,REJECT
DOMAIN-SUFFIX,cacafly.net,REJECT
DOMAIN-SUFFIX,cachead.com,REJECT
DOMAIN-SUFFIX,cachesit.com,REJECT
DOMAIN-SUFFIX,cadvv.heraldm.com,REJECT
DOMAIN-SUFFIX,cadvv.koreaherald.com,REJECT
DOMAIN-SUFFIX,caiyifz.com,REJECT
DOMAIN-SUFFIX,caliyuna.cn,REJECT
DOMAIN-SUFFIX,cams.pornrabbit.com,REJECT
DOMAIN-SUFFIX,cangnews.com,REJECT
DOMAIN-SUFFIX,canvas.thenextweb.com,REJECT
DOMAIN-SUFFIX,caob5.info,REJECT
DOMAIN-SUFFIX,caolvch.com,REJECT
DOMAIN-SUFFIX,cas.clickability.com,REJECT
DOMAIN-SUFFIX,casalemedia.com,REJECT
DOMAIN-SUFFIX,cash.neweramediaworks.com,REJECT
DOMAIN-SUFFIX,cayanfang.com,REJECT
DOMAIN-SUFFIX,cc.yac8.com,REJECT
DOMAIN-SUFFIX,ccbaihehq.com,REJECT
DOMAIN-SUFFIX,cccrir.com,REJECT
DOMAIN-SUFFIX,ccr.yxdown.com,REJECT
DOMAIN-SUFFIX,cctyly.com,REJECT
DOMAIN-SUFFIX,cdgxq.com,REJECT
DOMAIN-SUFFIX,cdn.jiuzhilan.com,REJECT
DOMAIN-SUFFIX,cdnads.com,REJECT
DOMAIN-SUFFIX,cdnmaster.com,REJECT
DOMAIN-SUFFIX,cdnny.com,REJECT
DOMAIN-SUFFIX,cdyqc.com,REJECT
DOMAIN-SUFFIX,cerebral.typn.com,REJECT
DOMAIN-SUFFIX,cgskqg.com,REJECT
DOMAIN-SUFFIX,chadegongxiao.com,REJECT
DOMAIN-SUFFIX,chance-ad.com,REJECT
DOMAIN-SUFFIX,chanet.com.cn,REJECT
DOMAIN-SUFFIX,changhehengqi.com,REJECT
DOMAIN-SUFFIX,chaoliangyun.com,REJECT
DOMAIN-SUFFIX,chartbeat.com,REJECT
DOMAIN-SUFFIX,chartboost.com,REJECT
DOMAIN-SUFFIX,chebse.com,REJECT
DOMAIN-SUFFIX,chengzhao95511.com,REJECT
DOMAIN-SUFFIX,chenwen7788.com,REJECT
DOMAIN-SUFFIX,chicken18.com,REJECT
DOMAIN-SUFFIX,chidir.com,REJECT
DOMAIN-SUFFIX,chinacsky.com,REJECT
DOMAIN-SUFFIX,chinaheh.com,REJECT
DOMAIN-SUFFIX,chinauma.net,REJECT
DOMAIN-SUFFIX,chinaweichu.net,REJECT
DOMAIN-SUFFIX,chmae.com,REJECT
DOMAIN-SUFFIX,chnhty.com,REJECT
DOMAIN-SUFFIX,chushoushijian.cn,REJECT
DOMAIN-SUFFIX,ciajingman.com,REJECT
DOMAIN-SUFFIX,cindy17club.com,REJECT
DOMAIN-SUFFIX,ciyitan.com,REJECT
DOMAIN-SUFFIX,cjmooter.xcache.kinxcdn.com,REJECT
DOMAIN-SUFFIX,clarity.abacast.com,REJECT
DOMAIN-SUFFIX,click.bokecc.com,REJECT
DOMAIN-SUFFIX,click.eyk.net,REJECT
DOMAIN-SUFFIX,click.hay3s.com,REJECT
DOMAIN-SUFFIX,click.hunantv.com,REJECT
DOMAIN-SUFFIX,click.livedoor.com,REJECT
DOMAIN-SUFFIX,clicks.superpages.com,REJECT
DOMAIN-SUFFIX,clickstrip.6wav.es,REJECT
DOMAIN-SUFFIX,clicktracks.com,REJECT
DOMAIN-SUFFIX,clickzs.com,REJECT
DOMAIN-SUFFIX,client.88tours.com,REJECT
DOMAIN-SUFFIX,clkads.com,REJECT
DOMAIN-SUFFIX,clkrev.com,REJECT
DOMAIN-SUFFIX,cloudad.asia,REJECT
DOMAIN-SUFFIX,cloudmobi.net,REJECT
DOMAIN-SUFFIX,cm.baidu.com,REJECT
DOMAIN-SUFFIX,cmaxisolation.com,REJECT
DOMAIN-SUFFIX,cmcore.com,REJECT
DOMAIN-SUFFIX,cmp288.com,REJECT
DOMAIN-SUFFIX,cmslayue.com,REJECT
DOMAIN-SUFFIX,cnbole.net,REJECT
DOMAIN-SUFFIX,cncy8.com,REJECT
DOMAIN-SUFFIX,cnetwidget.creativemark.co.uk,REJECT
DOMAIN-SUFFIX,cnfanglei.com,REJECT
DOMAIN-SUFFIX,cnhbxx.com,REJECT
DOMAIN-SUFFIX,cnkok.com,REJECT
DOMAIN-SUFFIX,cnpinzhuo.com,REJECT
DOMAIN-SUFFIX,cnscdj.com,REJECT
DOMAIN-SUFFIX,cnsjx.net,REJECT
DOMAIN-SUFFIX,cnxad.net,REJECT
DOMAIN-SUFFIX,cnzhqs.com,REJECT
DOMAIN-SUFFIX,cnzz.com,REJECT
DOMAIN-SUFFIX,cnzz.com.so,REJECT
DOMAIN-SUFFIX,cnzzlink.com,REJECT
DOMAIN-SUFFIX,cocounion.com,REJECT
DOMAIN-SUFFIX,code.kaixinjiehun.com,REJECT
DOMAIN-SUFFIX,code.yqw88.com,REJECT
DOMAIN-SUFFIX,code222.com,REJECT
DOMAIN-SUFFIX,code668.com,REJECT
DOMAIN-SUFFIX,collector.githubapp.com,REJECT
DOMAIN-SUFFIX,collector.viki.io,REJECT
DOMAIN-SUFFIX,combine.urbanairship.com,REJECT
DOMAIN-SUFFIX,comesgo.com,REJECT
DOMAIN-SUFFIX,config.ioam.de,REJECT
DOMAIN-SUFFIX,config2.mparticle.com,REJECT
DOMAIN-SUFFIX,connect.summit.co.uk,REJECT
DOMAIN-SUFFIX,content.livesportmedia.eu,REJECT
DOMAIN-SUFFIX,content.streamplay.to,REJECT
DOMAIN-SUFFIX,coolguang.com,REJECT
DOMAIN-SUFFIX,cooolyi.cn,REJECT
DOMAIN-SUFFIX,cop.my,REJECT
DOMAIN-SUFFIX,coremetrics.com,REJECT
DOMAIN-SUFFIX,corocksi.com,REJECT
DOMAIN-SUFFIX,cosoyoo.com,REJECT
DOMAIN-SUFFIX,couqm.com.cn,REJECT
DOMAIN-SUFFIX,cp.greenxf.cn,REJECT
DOMAIN-SUFFIX,cp.jfcdns.com,REJECT
DOMAIN-SUFFIX,cpcv.cc,REJECT
DOMAIN-SUFFIX,cpm.amateurcommunity.com,REJECT
DOMAIN-SUFFIX,cpm.amateurcommunity.de,REJECT
DOMAIN-SUFFIX,cpm.cm.kankan.com,REJECT
DOMAIN-SUFFIX,cpm.cm.sandai.net,REJECT
DOMAIN-SUFFIX,cpmchina.co,REJECT
DOMAIN-SUFFIX,cpms.cc,REJECT
DOMAIN-SUFFIX,cpro.baidustatic.com,REJECT
DOMAIN-SUFFIX,cpro1.edushi.com,REJECT
DOMAIN-SUFFIX,cpv.channelray,REJECT
DOMAIN-SUFFIX,cpv6.com,REJECT
DOMAIN-SUFFIX,cpva.cc,REJECT
DOMAIN-SUFFIX,cpx24.com,REJECT
DOMAIN-SUFFIX,cqfangduan.com,REJECT
DOMAIN-SUFFIX,cqftonline.com,REJECT
DOMAIN-SUFFIX,cqhnm.com,REJECT
DOMAIN-SUFFIX,cqyhd.com,REJECT
DOMAIN-SUFFIX,crasheye.cn,REJECT
DOMAIN-SUFFIX,crdrjs.info,REJECT
DOMAIN-SUFFIX,cre99.com,REJECT
DOMAIN-SUFFIX,creatives.cliphunter.com,REJECT
DOMAIN-SUFFIX,creatives.inmotionhosting.com,REJECT
DOMAIN-SUFFIX,creatives.livejasmin.com,REJECT
DOMAIN-SUFFIX,creatives.pichunter.com,REJECT
DOMAIN-SUFFIX,creatives.summitconnect.co.uk,REJECT
DOMAIN-SUFFIX,criteo.com,REJECT
DOMAIN-SUFFIX,crl.microsoft.com,REJECT
DOMAIN-SUFFIX,cr-nielsen.com,REJECT
DOMAIN-SUFFIX,crosschannel.com,REJECT
DOMAIN-SUFFIX,crs.baidu.com,REJECT
DOMAIN-SUFFIX,csad.cc,REJECT
DOMAIN-SUFFIX,csi.gstatic.com,REJECT
DOMAIN-SUFFIX,csqiulong.com,REJECT
DOMAIN-SUFFIX,cstoa.com,REJECT
DOMAIN-SUFFIX,csxjys.com,REJECT
DOMAIN-SUFFIX,ctsywy.com,REJECT
DOMAIN-SUFFIX,customer-security.online,REJECT
DOMAIN-SUFFIX,cy123.cc,REJECT
DOMAIN-SUFFIX,cyacc.com,REJECT
DOMAIN-SUFFIX,cyad.cc,REJECT
DOMAIN-SUFFIX,cyad123.com,REJECT
DOMAIN-SUFFIX,cylinderlongcheng.com,REJECT
DOMAIN-SUFFIX,cyylove.com,REJECT
DOMAIN-SUFFIX,czdqhyo1.net,REJECT
DOMAIN-SUFFIX,czjiuding.cn,REJECT
DOMAIN-SUFFIX,czpush.com,REJECT
DOMAIN-SUFFIX,czpwm.com,REJECT
DOMAIN-SUFFIX,czxiangyue.com,REJECT
DOMAIN-SUFFIX,d.107788.com,REJECT
DOMAIN-SUFFIX,d.annarbor.com,REJECT
DOMAIN-SUFFIX,d.applovin.com,REJECT
DOMAIN-SUFFIX,d.businessinsider.com,REJECT
DOMAIN-SUFFIX,d.gossipcenter.com,REJECT
DOMAIN-SUFFIX,d.ligatus.com,REJECT
DOMAIN-SUFFIX,d.mingyihui.net,REJECT
DOMAIN-SUFFIX,d.taomato.com,REJECT
DOMAIN-SUFFIX,d.thelocal.com,REJECT
DOMAIN-SUFFIX,d.tonghua5.com,REJECT
DOMAIN-SUFFIX,d.xinshipu.com,REJECT
DOMAIN-SUFFIX,d.yjbys.com,REJECT
DOMAIN-SUFFIX,d0.xcar.com.cn,REJECT
DOMAIN-SUFFIX,d1.sina.com.cn,REJECT
DOMAIN-SUFFIX,d1ad.com,REJECT
DOMAIN-SUFFIX,d1zgderxoe1a.cloudfront.net,REJECT
DOMAIN-SUFFIX,d2.sina.com.cn,REJECT
DOMAIN-SUFFIX,d3.sina.com.cn,REJECT
DOMAIN-SUFFIX,d6.mobaders.com,REJECT
DOMAIN-SUFFIX,d8360.com,REJECT
DOMAIN-SUFFIX,da.hunantv.com,REJECT
DOMAIN-SUFFIX,da.mgtv.com,REJECT
DOMAIN-SUFFIX,dacash.streamplay.to,REJECT
DOMAIN-SUFFIX,dads.new.digg.com,REJECT
DOMAIN-SUFFIX,dahanedu.com,REJECT
DOMAIN-SUFFIX,dailydeals.amarillo.com,REJECT
DOMAIN-SUFFIX,dailydeals.augustachronicle.com,REJECT
DOMAIN-SUFFIX,dailydeals.brainerddispatch.com,REJECT
DOMAIN-SUFFIX,dailydeals.lubbockonline.com,REJECT
DOMAIN-SUFFIX,dailydeals.onlineathens.com,REJECT
DOMAIN-SUFFIX,dailydeals.savannahnow.com,REJECT
DOMAIN-SUFFIX,dailyvideo.securejoin.com,REJECT
DOMAIN-SUFFIX,daima.23yy.com,REJECT
DOMAIN-SUFFIX,daima.chazidian.com,REJECT
DOMAIN-SUFFIX,daima.diaoben.net,REJECT
DOMAIN-SUFFIX,daima.huoche.net,REJECT
DOMAIN-SUFFIX,daima.ijq.tv,REJECT
DOMAIN-SUFFIX,daima.youbian.com,REJECT
DOMAIN-SUFFIX,daima123.cc,REJECT
DOMAIN-SUFFIX,dairuqi.com,REJECT
DOMAIN-SUFFIX,dalianhengtai.com,REJECT
DOMAIN-SUFFIX,dandan11.top,REJECT
DOMAIN-SUFFIX,dandan13.top,REJECT
DOMAIN-SUFFIX,dandan15.top,REJECT
DOMAIN-SUFFIX,dante2007.com,REJECT
DOMAIN-SUFFIX,daoyoudao.com,REJECT
DOMAIN-SUFFIX,dart.clearchannel.com,REJECT
DOMAIN-SUFFIX,dashet.com,REJECT
DOMAIN-SUFFIX,data.apn.co.nz,REJECT
DOMAIN-SUFFIX,data.neuroxmedia.com,REJECT
DOMAIN-SUFFIX,datafastguru.info,REJECT
DOMAIN-SUFFIX,datouniao.com,REJECT
DOMAIN-SUFFIX,dawwx.com,REJECT
DOMAIN-SUFFIX,dazhantai.com,REJECT
DOMAIN-SUFFIX,dazhonghua.cn,REJECT
DOMAIN-SUFFIX,dbam.dashbida.com,REJECT
DOMAIN-SUFFIX,dbncp.com,REJECT
DOMAIN-SUFFIX,dbwmjj.com,REJECT
DOMAIN-SUFFIX,dcad.watersoul.com,REJECT
DOMAIN-SUFFIX,ddanq.com,REJECT
DOMAIN-SUFFIX,ddapp.cn,REJECT
DOMAIN-SUFFIX,ddd.yuyouge.com,REJECT
DOMAIN-SUFFIX,ddg1277.com,REJECT
DOMAIN-SUFFIX,ddhtek.com,REJECT
DOMAIN-SUFFIX,ddomm.com,REJECT
DOMAIN-SUFFIX,de.ioam.de,REJECT
DOMAIN-SUFFIX,deals.ledgertranscript.com,REJECT
DOMAIN-SUFFIX,dejing.laobanfa.com,REJECT
DOMAIN-SUFFIX,deliver.ifeng.com,REJECT
DOMAIN-SUFFIX,delivery.playallvideos.com,REJECT
DOMAIN-SUFFIX,delivery.porn.com,REJECT
DOMAIN-SUFFIX,delivery.thebloggernetwork.com,REJECT
DOMAIN-SUFFIX,delivery.wasu.cn,REJECT
DOMAIN-SUFFIX,desk.cmix.org,REJECT
DOMAIN-SUFFIX,detuns.com,REJECT
DOMAIN-SUFFIX,dezfu.com,REJECT
DOMAIN-SUFFIX,df3n43m.com,REJECT
DOMAIN-SUFFIX,df77.com,REJECT
DOMAIN-SUFFIX,dfad.dfdaily.com,REJECT
DOMAIN-SUFFIX,dgpzx.com,REJECT
DOMAIN-SUFFIX,dhxyzx.cn,REJECT
DOMAIN-SUFFIX,dianjoy.com,REJECT
DOMAIN-SUFFIX,dianru.com,REJECT
DOMAIN-SUFFIX,diaojiaoji168.com,REJECT
DOMAIN-SUFFIX,digdug.divxnetworks.com,REJECT
DOMAIN-SUFFIX,dimg1.sz.net.cn,REJECT
DOMAIN-SUFFIX,dingon.com.cn,REJECT
DOMAIN-SUFFIX,dipan.com,REJECT
DOMAIN-SUFFIX,directrev.com,REJECT
DOMAIN-SUFFIX,dis.crieto.com,REJECT
DOMAIN-SUFFIX,display.digitalriver.com,REJECT
DOMAIN-SUFFIX,display.superbay.net,REJECT
DOMAIN-SUFFIX,distf.kankan.com,REJECT
DOMAIN-SUFFIX,diyijs.duapp.com,REJECT
DOMAIN-SUFFIX,djs.baomihua.com,REJECT
DOMAIN-SUFFIX,dkdlsj.com,REJECT
DOMAIN-SUFFIX,dleke.com,REJECT
DOMAIN-SUFFIX,dlpifu.com,REJECT
DOMAIN-SUFFIX,dlrijiaele.com,REJECT
DOMAIN-SUFFIX,dlzjdesign.com,REJECT
DOMAIN-SUFFIX,dm.388g.cc,REJECT
DOMAIN-SUFFIX,dm.51okc.com,REJECT
DOMAIN-SUFFIX,dm.92to.com,REJECT
DOMAIN-SUFFIX,dm.aizhan.com,REJECT
DOMAIN-SUFFIX,dm.chalook.net,REJECT
DOMAIN-SUFFIX,dm.jb51.net,REJECT
DOMAIN-SUFFIX,dm.jsyst.cn,REJECT
DOMAIN-SUFFIX,dm.jy135.com,REJECT
DOMAIN-SUFFIX,dm.ppzuowen.com,REJECT
DOMAIN-SUFFIX,dm.pw0.cn,REJECT
DOMAIN-SUFFIX,dm.riji.cn,REJECT
DOMAIN-SUFFIX,dm.sanwen.net,REJECT
DOMAIN-SUFFIX,dm.sanwen8.com,REJECT
DOMAIN-SUFFIX,dm.sb580.com,REJECT
DOMAIN-SUFFIX,dm.ws8.org,REJECT
DOMAIN-SUFFIX,dm.yjbys.com,REJECT
DOMAIN-SUFFIX,dm1.tom61.com,REJECT
DOMAIN-SUFFIX,dm50.jkyd.net,REJECT
DOMAIN-SUFFIX,dmp.sina.cn,REJECT
DOMAIN-SUFFIX,dmt.qcrx.cn,REJECT
DOMAIN-SUFFIX,dmtrck.com,REJECT
DOMAIN-SUFFIX,dn3.ixinwei.com,REJECT
DOMAIN-SUFFIX,dn7788.com,REJECT
DOMAIN-SUFFIX,dnvus.com,REJECT
DOMAIN-SUFFIX,domob.cn,REJECT
DOMAIN-SUFFIX,domob.com.cn,REJECT
DOMAIN-SUFFIX,domob.org,REJECT
DOMAIN-SUFFIX,dontblockme.modaco.com,REJECT
DOMAIN-SUFFIX,dot.eporner.com,REJECT
DOMAIN-SUFFIX,dot2.eporner.com,REJECT
DOMAIN-SUFFIX,dotmore.com.tw,REJECT
DOMAIN-SUFFIX,dou777.com,REJECT
DOMAIN-SUFFIX,doubleclick.tv002.com,REJECT
DOMAIN-SUFFIX,doubleverify.com,REJECT
DOMAIN-SUFFIX,doudao.cn,REJECT
DOMAIN-SUFFIX,dragoncent.com,REJECT
DOMAIN-SUFFIX,dreamfull.cn,REJECT
DOMAIN-SUFFIX,drlsf.com,REJECT
DOMAIN-SUFFIX,drxrc.com,REJECT
DOMAIN-SUFFIX,dshrx.com,REJECT
DOMAIN-SUFFIX,dsxdn.com,REJECT
DOMAIN-SUFFIX,dtrk.slimcdn.com,REJECT
DOMAIN-SUFFIX,duanat.com,REJECT
DOMAIN-SUFFIX,duapp.com,REJECT
DOMAIN-SUFFIX,dugbvb.com,REJECT
DOMAIN-SUFFIX,duiwai.baidu.com,REJECT
DOMAIN-SUFFIX,dumedia.ru,REJECT
DOMAIN-SUFFIX,duomeng.cn,REJECT
DOMAIN-SUFFIX,duomeng.net,REJECT
DOMAIN-SUFFIX,duomeng.org,REJECT
DOMAIN-SUFFIX,duoyidd.com,REJECT
DOMAIN-SUFFIX,dup.baidustatic.com,REJECT
DOMAIN-SUFFIX,dushimj.com,REJECT
DOMAIN-SUFFIX,duusuu.com,REJECT
DOMAIN-SUFFIX,duyihu.net,REJECT
DOMAIN-SUFFIX,dvr8.com,REJECT
DOMAIN-SUFFIX,dvs.china.com,REJECT
DOMAIN-SUFFIX,dvser.china.com,REJECT
DOMAIN-SUFFIX,dw998.com,REJECT
DOMAIN-SUFFIX,dx1200.com,REJECT
DOMAIN-SUFFIX,dxpmedia.com,REJECT
DOMAIN-SUFFIX,dxssiyi.com,REJECT
DOMAIN-SUFFIX,dydab.com,REJECT
DOMAIN-SUFFIX,dyn.tnaflix.com,REJECT
DOMAIN-SUFFIX,dzais.com,REJECT
DOMAIN-SUFFIX,dzisou.com,REJECT
DOMAIN-SUFFIX,dzjzg.com,REJECT
DOMAIN-SUFFIX,e.nexac.com,REJECT
DOMAIN-SUFFIX,e.qiaoyuwang.com,REJECT
DOMAIN-SUFFIX,e.yycqc.com,REJECT
DOMAIN-SUFFIX,e7001.com,REJECT
DOMAIN-SUFFIX,e7002.com,REJECT
DOMAIN-SUFFIX,e7009.com,REJECT
DOMAIN-SUFFIX,e70123.com,REJECT
DOMAIN-SUFFIX,e7015.com,REJECT
DOMAIN-SUFFIX,e704.net,REJECT
DOMAIN-SUFFIX,e705.net,REJECT
DOMAIN-SUFFIX,e706.net,REJECT
DOMAIN-SUFFIX,e708.net,REJECT
DOMAIN-SUFFIX,e719.net,REJECT
DOMAIN-SUFFIX,e9377f.com,REJECT
DOMAIN-SUFFIX,eacash.streamplay.to,REJECT
DOMAIN-SUFFIX,eap.big5.enorth.com.cn,REJECT
DOMAIN-SUFFIX,eap.enorth.com.cn,REJECT
DOMAIN-SUFFIX,eclick.baidu.com,REJECT
DOMAIN-SUFFIX,eclkspbn.com,REJECT
DOMAIN-SUFFIX,ecma.bdimg.com,REJECT
DOMAIN-SUFFIX,ecmb.bdimg.com,REJECT
DOMAIN-SUFFIX,ecuc123.net,REJECT
DOMAIN-SUFFIX,edigitalsurvey.com,REJECT
DOMAIN-SUFFIX,edncui.net,REJECT
DOMAIN-SUFFIX,eduancm.com,REJECT
DOMAIN-SUFFIX,eduzzjy.com,REJECT
DOMAIN-SUFFIX,ee4kdushuba.com,REJECT
DOMAIN-SUFFIX,eeee500.com,REJECT
DOMAIN-SUFFIX,effectivemeasure.com,REJECT
DOMAIN-SUFFIX,ekeide.com,REJECT
DOMAIN-SUFFIX,emarbox.com,REJECT
DOMAIN-SUFFIX,ent1.12584.cn,REJECT
DOMAIN-SUFFIX,epowernetworktrackerimages.s3.amazonaws.com,REJECT
DOMAIN-SUFFIX,erebor.douban.com,REJECT
DOMAIN-SUFFIX,eteun.cn,REJECT
DOMAIN-SUFFIX,euwidget.imshopping.com,REJECT
DOMAIN-SUFFIX,eva.ucas.com,REJECT
DOMAIN-SUFFIX,evefashion.cn,REJECT
DOMAIN-SUFFIX,events.kalooga.com,REJECT
DOMAIN-SUFFIX,exit.macandbumble.com,REJECT
DOMAIN-SUFFIX,exoclick.com,REJECT
DOMAIN-SUFFIX,exosrv.com,REJECT
DOMAIN-SUFFIX,exp.17wo.cn,REJECT
DOMAIN-SUFFIX,expo123.net,REJECT
DOMAIN-SUFFIX,ext.theglobalweb.com,REJECT
DOMAIN-SUFFIX,eyd77s.com,REJECT
DOMAIN-SUFFIX,eye.swfchan.com,REJECT
DOMAIN-SUFFIX,eyouv.cn,REJECT
DOMAIN-SUFFIX,ez33.org.cn,REJECT
DOMAIN-SUFFIX,ezucods.cn,REJECT
DOMAIN-SUFFIX,f.520tingshu.com,REJECT
DOMAIN-SUFFIX,f1.06ps.com,REJECT
DOMAIN-SUFFIX,f1.bizhiku.net,REJECT
DOMAIN-SUFFIX,f1.meishichina.com,REJECT
DOMAIN-SUFFIX,f1.pig66.com,REJECT
DOMAIN-SUFFIX,f1190.com,REJECT
DOMAIN-SUFFIX,f2zd.com,REJECT
DOMAIN-SUFFIX,f6ce.com,REJECT
DOMAIN-SUFFIX,f70123.com,REJECT
DOMAIN-SUFFIX,f8272.com,REJECT
DOMAIN-SUFFIX,facebookma.cn,REJECT
DOMAIN-SUFFIX,fan.twitch.tv,REJECT
DOMAIN-SUFFIX,fancyapi.com,REJECT
DOMAIN-SUFFIX,fansi365.com,REJECT
DOMAIN-SUFFIX,farm.plista.com,REJECT
DOMAIN-SUFFIX,fastable.com,REJECT
DOMAIN-SUFFIX,fastapi.net,REJECT
DOMAIN-SUFFIX,fastclick.net,REJECT
DOMAIN-SUFFIX,fastly.bench.cedexis.com,REJECT
DOMAIN-SUFFIX,fcsass.org.cn,REJECT
DOMAIN-SUFFIX,fd7c.com,REJECT
DOMAIN-SUFFIX,feeds.logicbuy.com,REJECT
DOMAIN-SUFFIX,feeds.videosz.com,REJECT
DOMAIN-SUFFIX,feidalu.com,REJECT
DOMAIN-SUFFIX,feifish66.com,REJECT
DOMAIN-SUFFIX,feixin2.com,REJECT
DOMAIN-SUFFIX,fembsflungod.com,REJECT
DOMAIN-SUFFIX,fenggejiaju.com,REJECT
DOMAIN-SUFFIX,ff.nsg.org.ua,REJECT
DOMAIN-SUFFIX,fff.yuyouge.com,REJECT
DOMAIN-SUFFIX,ffhtek.com,REJECT
DOMAIN-SUFFIX,fimserve.ign.com,REJECT
DOMAIN-SUFFIX,findicons.com,REJECT
DOMAIN-SUFFIX,finding.hardwareheaven.com,REJECT
DOMAIN-SUFFIX,findnsave.idahostatesman.com,REJECT
DOMAIN-SUFFIX,firefang.cn,REJECT
DOMAIN-SUFFIX,fjkst.com,REJECT
DOMAIN-SUFFIX,fjmeyer.com,REJECT
DOMAIN-SUFFIX,flashtalking.com,REJECT
DOMAIN-SUFFIX,flowcodeapp.com,REJECT
DOMAIN-SUFFIX,flurry.com,REJECT
DOMAIN-SUFFIX,fmgoal.com,REJECT
DOMAIN-SUFFIX,fnkjj.com,REJECT
DOMAIN-SUFFIX,focusprolight.com,REJECT
DOMAIN-SUFFIX,fotao9.com,REJECT
DOMAIN-SUFFIX,founseezb.cn,REJECT
DOMAIN-SUFFIX,fpb.51edu.com,REJECT
DOMAIN-SUFFIX,fqtra.com,REJECT
DOMAIN-SUFFIX,freexxxvideoclip.aebn.net,REJECT
DOMAIN-SUFFIX,fst360.com,REJECT
DOMAIN-SUFFIX,fsyzcs.com,REJECT
DOMAIN-SUFFIX,ft.pnop.com,REJECT
DOMAIN-SUFFIX,fthcz.com,REJECT
DOMAIN-SUFFIX,fu68.com,REJECT
DOMAIN-SUFFIX,fucnm.com,REJECT
DOMAIN-SUFFIX,fujianryt.com,REJECT
DOMAIN-SUFFIX,fun.ynet.com,REJECT
DOMAIN-SUFFIX,fv99.com,REJECT
DOMAIN-SUFFIX,fwt0.com,REJECT
DOMAIN-SUFFIX,fxmacd.com,REJECT
DOMAIN-SUFFIX,fxtducb.cn,REJECT
DOMAIN-SUFFIX,fxxgw.com,REJECT
DOMAIN-SUFFIX,fydgold132.com,REJECT
DOMAIN-SUFFIX,fytza.cn,REJECT
DOMAIN-SUFFIX,g.brothersoft.com,REJECT
DOMAIN-SUFFIX,g.chuiyao.com,REJECT
DOMAIN-SUFFIX,g.hsw.cn,REJECT
DOMAIN-SUFFIX,g.mnw.cn,REJECT
DOMAIN-SUFFIX,g.ousns.net,REJECT
DOMAIN-SUFFIX,g1.0573ren.com,REJECT
DOMAIN-SUFFIX,g1.taijuba.com,REJECT
DOMAIN-SUFFIX,g1c5.com,REJECT
DOMAIN-SUFFIX,g1f5.com,REJECT
DOMAIN-SUFFIX,g2.ousns.net,REJECT
DOMAIN-SUFFIX,gadwhy.com,REJECT
DOMAIN-SUFFIX,gameads.digyourowngrave.com,REJECT
DOMAIN-SUFFIX,gateway.fortunelounge.com,REJECT
DOMAIN-SUFFIX,gateways.s3.amazonaws.com,REJECT
DOMAIN-SUFFIX,gd.jqgc.com,REJECT
DOMAIN-SUFFIX,gd.vodtw.com,REJECT
DOMAIN-SUFFIX,gdbly.com,REJECT
DOMAIN-SUFFIX,gdskywings.com,REJECT
DOMAIN-SUFFIX,gdsqwy.org,REJECT
DOMAIN-SUFFIX,ge95.com,REJECT
DOMAIN-SUFFIX,geili.co,REJECT
DOMAIN-SUFFIX,geo.cliphunter.com,REJECT
DOMAIN-SUFFIX,geo.connexionsecure.com,REJECT
DOMAIN-SUFFIX,geo.frtya.com,REJECT
DOMAIN-SUFFIX,geo.frtyd.com,REJECT
DOMAIN-SUFFIX,geobanner.adultfriendfinder.com,REJECT
DOMAIN-SUFFIX,geobanner.alt.com,REJECT
DOMAIN-SUFFIX,geobanner.friendfinder.com,REJECT
DOMAIN-SUFFIX,geobanner.passion.com,REJECT
DOMAIN-SUFFIX,geobanner.socialflirt.com,REJECT
DOMAIN-SUFFIX,geoshopping.nzherald.co.nz,REJECT
DOMAIN-SUFFIX,geryi.com,REJECT
DOMAIN-SUFFIX,get.thefile.me,REJECT
DOMAIN-SUFFIX,getnormalizedurl.com,REJECT
DOMAIN-SUFFIX,getui.com,REJECT
DOMAIN-SUFFIX,gewuwen.com,REJECT
DOMAIN-SUFFIX,gf108.com,REJECT
DOMAIN-SUFFIX,gf1352.com,REJECT
DOMAIN-SUFFIX,gfd80.com,REJECT
DOMAIN-SUFFIX,gfx.infomine.com,REJECT
DOMAIN-SUFFIX,gg.0598yu.com,REJECT
DOMAIN-SUFFIX,gg.blueidea.com,REJECT
DOMAIN-SUFFIX,gg.cs090.com,REJECT
DOMAIN-SUFFIX,gg.gao7.com,REJECT
DOMAIN-SUFFIX,gg.huangye88.com,REJECT
DOMAIN-SUFFIX,gg.jkmeishi.com,REJECT
DOMAIN-SUFFIX,gg.sonhoo.com,REJECT
DOMAIN-SUFFIX,gg.uuu9.com,REJECT
DOMAIN-SUFFIX,gg.xingzuo360.cn,REJECT
DOMAIN-SUFFIX,gg.yxdown.com,REJECT
DOMAIN-SUFFIX,gg.zhongyao1.com,REJECT
DOMAIN-SUFFIX,gg0376.com,REJECT
DOMAIN-SUFFIX,gg1.yszyz.com,REJECT
DOMAIN-SUFFIX,gg2.dss9927.com,REJECT
DOMAIN-SUFFIX,gg570.com,REJECT
DOMAIN-SUFFIX,gg86.pinggu.org,REJECT
DOMAIN-SUFFIX,ggcode.2345.com,REJECT
DOMAIN-SUFFIX,ggdoubi.com,REJECT
DOMAIN-SUFFIX,ggg.zj.com,REJECT
DOMAIN-SUFFIX,ggmm777.com,REJECT
DOMAIN-SUFFIX,ggs.myzaker.com,REJECT
DOMAIN-SUFFIX,ggw.gusuwang.com,REJECT
DOMAIN-SUFFIX,ggw.watertu.com,REJECT
DOMAIN-SUFFIX,ggxt.net,REJECT
DOMAIN-SUFFIX,gjghy.com,REJECT
DOMAIN-SUFFIX,glasszz.com,REJECT
DOMAIN-SUFFIX,gm682.com,REJECT
DOMAIN-SUFFIX,gma1.com,REJECT
DOMAIN-SUFFIX,go.hangzhou.com.cn,REJECT
DOMAIN-SUFFIX,godloveme.cn,REJECT
DOMAIN-SUFFIX,go-mpulse.net,REJECT
DOMAIN-SUFFIX,googeadsserving.cn,REJECT
DOMAIN-SUFFIX,googlealiyun.cn,REJECT
DOMAIN-SUFFIX,googletakes.com,REJECT
DOMAIN-SUFFIX,goto.www.iciba.com,REJECT
DOMAIN-SUFFIX,gotourl.xyz,REJECT
DOMAIN-SUFFIX,gouzhibao.cn,REJECT
DOMAIN-SUFFIX,govgift.com,REJECT
DOMAIN-SUFFIX,govids.net,REJECT
DOMAIN-SUFFIX,gp.jstv.com,REJECT
DOMAIN-SUFFIX,gqswg.com,REJECT
DOMAIN-SUFFIX,greenhouseglobal.cn,REJECT
DOMAIN-SUFFIX,groupa.onlylady.com,REJECT
DOMAIN-SUFFIX,gs307.com,REJECT
DOMAIN-SUFFIX,gso0.com,REJECT
DOMAIN-SUFFIX,gt.yy.com,REJECT
DOMAIN-SUFFIX,gtags.net,REJECT
DOMAIN-SUFFIX,gtmucs.cn,REJECT
DOMAIN-SUFFIX,guang.lesports.com,REJECT
DOMAIN-SUFFIX,guang.sdsgwy.com,REJECT
DOMAIN-SUFFIX,guangzizai.com,REJECT
DOMAIN-SUFFIX,guduopu.com,REJECT
DOMAIN-SUFFIX,guess.h.qhimg.com,REJECT
DOMAIN-SUFFIX,gugulonger.cn,REJECT
DOMAIN-SUFFIX,guidashu.com,REJECT
DOMAIN-SUFFIX,guohead.com,REJECT
DOMAIN-SUFFIX,guomob.com,REJECT
DOMAIN-SUFFIX,guoshennet.com,REJECT
DOMAIN-SUFFIX,gw630.com,REJECT
DOMAIN-SUFFIX,gydag.com,REJECT
DOMAIN-SUFFIX,gyrtg.com,REJECT
DOMAIN-SUFFIX,gzktpf.com,REJECT
DOMAIN-SUFFIX,gzlykj.cn,REJECT
DOMAIN-SUFFIX,gzmjnx.cn,REJECT
DOMAIN-SUFFIX,gzqudou.com,REJECT
DOMAIN-SUFFIX,h9377c.com,REJECT
DOMAIN-SUFFIX,haiwengji.net,REJECT
DOMAIN-SUFFIX,haiyunpush.com,REJECT
DOMAIN-SUFFIX,hanju18.net,REJECT
DOMAIN-SUFFIX,hao123rt.com,REJECT
DOMAIN-SUFFIX,hao61.net,REJECT
DOMAIN-SUFFIX,haoghost.com,REJECT
DOMAIN-SUFFIX,haohaowan8.com,REJECT
DOMAIN-SUFFIX,haolew.com,REJECT
DOMAIN-SUFFIX,hapic1.jhkxwl.com,REJECT
DOMAIN-SUFFIX,hascosafety.com,REJECT
DOMAIN-SUFFIX,hbalx.cn,REJECT
DOMAIN-SUFFIX,hbngfy.com,REJECT
DOMAIN-SUFFIX,hbyyzm.com,REJECT
DOMAIN-SUFFIX,hccms.com.cn,REJECT
DOMAIN-SUFFIX,hcreditx.com,REJECT
DOMAIN-SUFFIX,hdad.baike.com,REJECT
DOMAIN-SUFFIX,heavenmedia.v3g4s.com,REJECT
DOMAIN-SUFFIX,hefan365.com,REJECT
DOMAIN-SUFFIX,heib10.top,REJECT
DOMAIN-SUFFIX,heib12.top,REJECT
DOMAIN-SUFFIX,hejban.youwatch.org,REJECT
DOMAIN-SUFFIX,hejingroup.cn,REJECT
DOMAIN-SUFFIX,hesxz.com,REJECT
DOMAIN-SUFFIX,hfjuki.com,REJECT
DOMAIN-SUFFIX,hfsteel.net,REJECT
DOMAIN-SUFFIX,hh6666.com,REJECT
DOMAIN-SUFFIX,hhly88.com,REJECT
DOMAIN-SUFFIX,hhppyt.com,REJECT
DOMAIN-SUFFIX,hi760.com,REJECT
DOMAIN-SUFFIX,hi9377.com,REJECT
DOMAIN-SUFFIX,hiad.myweb.hinet.net,REJECT
DOMAIN-SUFFIX,hiad.vmall.com,REJECT
DOMAIN-SUFFIX,hivecn.cn,REJECT
DOMAIN-SUFFIX,hk9600.com,REJECT
DOMAIN-SUFFIX,hkfuy.com,REJECT
DOMAIN-SUFFIX,hmp33.com,REJECT
DOMAIN-SUFFIX,hmttoly.com,REJECT
DOMAIN-SUFFIX,hmyangshengji.com,REJECT
DOMAIN-SUFFIX,hnasd.com,REJECT
DOMAIN-SUFFIX,hnctsm.com,REJECT
DOMAIN-SUFFIX,hnol.net,REJECT
DOMAIN-SUFFIX,hnyny.com,REJECT
DOMAIN-SUFFIX,home520.com,REJECT
DOMAIN-SUFFIX,hosting.miarroba.info,REJECT
DOMAIN-SUFFIX,houdaolj.com,REJECT
DOMAIN-SUFFIX,hqgjcm.com,REJECT
DOMAIN-SUFFIX,hr41.cn,REJECT
DOMAIN-SUFFIX,hr44.com,REJECT
DOMAIN-SUFFIX,hslyqs.com,REJECT
DOMAIN-SUFFIX,htjsk.com,REJECT
DOMAIN-SUFFIX,huahuaka.com,REJECT
DOMAIN-SUFFIX,huashengtai.net,REJECT
DOMAIN-SUFFIX,huashuowork.com,REJECT
DOMAIN-SUFFIX,huayi65.com,REJECT
DOMAIN-SUFFIX,hubojd.com,REJECT
DOMAIN-SUFFIX,huimee.net,REJECT
DOMAIN-SUFFIX,humanding.com,REJECT
DOMAIN-SUFFIX,hw6.com,REJECT
DOMAIN-SUFFIX,hxadt.com,REJECT
DOMAIN-SUFFIX,hxjs.tool.hexun.com,REJECT
DOMAIN-SUFFIX,hxsame.hexun.com,REJECT
DOMAIN-SUFFIX,hxspc.com,REJECT
DOMAIN-SUFFIX,hxyifu.com,REJECT
DOMAIN-SUFFIX,hxyx360.com,REJECT
DOMAIN-SUFFIX,hys4.com,REJECT
DOMAIN-SUFFIX,hystq.com,REJECT
DOMAIN-SUFFIX,hytgj.com,REJECT
DOMAIN-SUFFIX,hyz86.com,REJECT
DOMAIN-SUFFIX,hz.shouyoutv.com,REJECT
DOMAIN-SUFFIX,hzaibi.com,REJECT
DOMAIN-SUFFIX,hzhyhm.com,REJECT
DOMAIN-SUFFIX,i.syasn.com,REJECT
DOMAIN-SUFFIX,i.zhuoyaju.com,REJECT
DOMAIN-SUFFIX,i3818.com,REJECT
DOMAIN-SUFFIX,i92xue.com,REJECT
DOMAIN-SUFFIX,iadc.qwapi.com,REJECT
DOMAIN-SUFFIX,iads.xinmin.cn,REJECT
DOMAIN-SUFFIX,iadsdk.apple.com,REJECT
DOMAIN-SUFFIX,ibanners.empoweredcomms.com.au,REJECT
DOMAIN-SUFFIX,icast.cn,REJECT
DOMAIN-SUFFIX,id528.com,REJECT
DOMAIN-SUFFIX,idasai.com,REJECT
DOMAIN-SUFFIX,idcot.com,REJECT
DOMAIN-SUFFIX,idcqi.com,REJECT
DOMAIN-SUFFIX,identified.cn,REJECT
DOMAIN-SUFFIX,idianfang.com,REJECT
DOMAIN-SUFFIX,if1512.com,REJECT
DOMAIN-SUFFIX,iframe.travel.yahoo.com,REJECT
DOMAIN-SUFFIX,ih.adscale.de,REJECT
DOMAIN-SUFFIX,ihualun.com,REJECT
DOMAIN-SUFFIX,iia1.pikacn.com,REJECT
DOMAIN-SUFFIX,iii.6park.com,REJECT
DOMAIN-SUFFIX,il8r.com,REJECT
DOMAIN-SUFFIX,im.ov.yahoo.co.jp,REJECT
DOMAIN-SUFFIX,im1.56zzw.com,REJECT
DOMAIN-SUFFIX,ima3vpaid.appspot.com,REJECT
DOMAIN-SUFFIX,imads.rediff.com,REJECT
DOMAIN-SUFFIX,image.9duw.com,REJECT
DOMAIN-SUFFIX,image.gentags.com,REJECT
DOMAIN-SUFFIX,image.hh010.com,REJECT
DOMAIN-SUFFIX,images.chinaz.com,REJECT
DOMAIN-SUFFIX,images.gxsky.com,REJECT
DOMAIN-SUFFIX,imageter.com,REJECT
DOMAIN-SUFFIX,imedia.bokecc.com,REJECT
DOMAIN-SUFFIX,imeijiajia.com,REJECT
DOMAIN-SUFFIX,img.3sjt.com,REJECT
DOMAIN-SUFFIX,img.9duw.com,REJECT
DOMAIN-SUFFIX,img.libdd.com,REJECT
DOMAIN-SUFFIX,img.meipic.net,REJECT
DOMAIN-SUFFIX,img.qdscgj.com,REJECT
DOMAIN-SUFFIX,img.xuenb.com,REJECT
DOMAIN-SUFFIX,img.zuowen8.com,REJECT
DOMAIN-SUFFIX,img09.zhaopin.com,REJECT
DOMAIN-SUFFIX,img1.jintang114.org,REJECT
DOMAIN-SUFFIX,img3.126.net,REJECT
DOMAIN-SUFFIX,img6.126.net,REJECT
DOMAIN-SUFFIX,img80.net,REJECT
DOMAIN-SUFFIX,immob.cn,REJECT
DOMAIN-SUFFIX,imneinei.com,REJECT
DOMAIN-SUFFIX,i-mobile.co.jp,REJECT
DOMAIN-SUFFIX,imrworldwide.com,REJECT
DOMAIN-SUFFIX,in.zog.link,REJECT
DOMAIN-SUFFIX,inad.com,REJECT
DOMAIN-SUFFIX,inccnd.com,REJECT
DOMAIN-SUFFIX,indieclick.3janecdn.com,REJECT
DOMAIN-SUFFIX,inmobi.com,REJECT
DOMAIN-SUFFIX,inmobicdn.net,REJECT
DOMAIN-SUFFIX,innity.com,REJECT
DOMAIN-SUFFIX,innity.net,REJECT
DOMAIN-SUFFIX,insenz.com,REJECT
DOMAIN-SUFFIX,inskin.vo.llnwd.net,REJECT
DOMAIN-SUFFIX,instabug.com,REJECT
DOMAIN-SUFFIX,instreet.cn,REJECT
DOMAIN-SUFFIX,intely.cn,REJECT
DOMAIN-SUFFIX,iperceptions.com,REJECT
DOMAIN-SUFFIX,ipic.staticsdo.com,REJECT
DOMAIN-SUFFIX,ipinyou.com,REJECT
DOMAIN-SUFFIX,iroby.com,REJECT
DOMAIN-SUFFIX,irs01.com,REJECT
DOMAIN-SUFFIX,irs01.net,REJECT
DOMAIN-SUFFIX,irs09.com,REJECT
DOMAIN-SUFFIX,isdspeed.qq.com,REJECT
DOMAIN-SUFFIX,ishowbg.com,REJECT
DOMAIN-SUFFIX,istreamsche.com,REJECT
DOMAIN-SUFFIX,itv.hexun.com,REJECT
DOMAIN-SUFFIX,iuuff.com,REJECT
DOMAIN-SUFFIX,ivy.pconline.com.cn,REJECT
DOMAIN-SUFFIX,iwanad.baidu.com,REJECT
DOMAIN-SUFFIX,ixpub.net,REJECT
DOMAIN-SUFFIX,j.6avz.com,REJECT
DOMAIN-SUFFIX,j.ccnovel.com,REJECT
DOMAIN-SUFFIX,j1.piaobing.com,REJECT
DOMAIN-SUFFIX,ja.gamersky.com,REJECT
DOMAIN-SUFFIX,ja1.gamersky.com,REJECT
DOMAIN-SUFFIX,ja9377.com,REJECT
DOMAIN-SUFFIX,jackaow.com,REJECT
DOMAIN-SUFFIX,jb.dianshu119.com,REJECT
DOMAIN-SUFFIX,jb.ecar168.cn,REJECT
DOMAIN-SUFFIX,jb.tupianzj.com,REJECT
DOMAIN-SUFFIX,jbyy010.com,REJECT
DOMAIN-SUFFIX,jc1.dayfund.cn,REJECT
DOMAIN-SUFFIX,jczzjx.com,REJECT
DOMAIN-SUFFIX,jddaw.com,REJECT
DOMAIN-SUFFIX,jdlcg.cn,REJECT
DOMAIN-SUFFIX,jdlhg.com,REJECT
DOMAIN-SUFFIX,jermr.com,REJECT
DOMAIN-SUFFIX,jesgoo.com,REJECT
DOMAIN-SUFFIX,jfqkj.com,REJECT
DOMAIN-SUFFIX,jgchq.com,REJECT
DOMAIN-SUFFIX,jghcy.com,REJECT
DOMAIN-SUFFIX,jhakie.com,REJECT
DOMAIN-SUFFIX,jhtcdj.com,REJECT
DOMAIN-SUFFIX,jhzl001.com,REJECT
DOMAIN-SUFFIX,jiaheyonggu.com,REJECT
DOMAIN-SUFFIX,jiajv.net,REJECT
DOMAIN-SUFFIX,jianbaimei.com,REJECT
DOMAIN-SUFFIX,jianmei123.com,REJECT
DOMAIN-SUFFIX,jiaoben.eastday.com,REJECT
DOMAIN-SUFFIX,jiaoben.ganji.cn,REJECT
DOMAIN-SUFFIX,jiaoben.jucanw.com,REJECT
DOMAIN-SUFFIX,jiaoben.junmeng.com,REJECT
DOMAIN-SUFFIX,jiathis.com,REJECT
DOMAIN-SUFFIX,jiawen88.com,REJECT
DOMAIN-SUFFIX,jiehantai.com,REJECT
DOMAIN-SUFFIX,jiehunmishu.com,REJECT
DOMAIN-SUFFIX,jifeidandar.com,REJECT
DOMAIN-SUFFIX,jiguang.cn,REJECT
DOMAIN-SUFFIX,jimdo.com,REJECT
DOMAIN-SUFFIX,jimeilm.com,REJECT
DOMAIN-SUFFIX,jindu179.com,REJECT
DOMAIN-SUFFIX,jinghuazhijia.com,REJECT
DOMAIN-SUFFIX,jiuku.cc,REJECT
DOMAIN-SUFFIX,jiyou2014.com,REJECT
DOMAIN-SUFFIX,jizzads.com,REJECT
DOMAIN-SUFFIX,jjxgly.com,REJECT
DOMAIN-SUFFIX,jk939.com,REJECT
DOMAIN-SUFFIX,jkjjkj.top,REJECT
DOMAIN-SUFFIX,jkmxy.com,REJECT
DOMAIN-SUFFIX,jl027.com,REJECT
DOMAIN-SUFFIX,jlssbz.com,REJECT
DOMAIN-SUFFIX,jmsyzj.com,REJECT
DOMAIN-SUFFIX,jndczg.com,REJECT
DOMAIN-SUFFIX,jnrsjm.com,REJECT
DOMAIN-SUFFIX,jnsdkjzs.com,REJECT
DOMAIN-SUFFIX,jnsz.net.cn,REJECT
DOMAIN-SUFFIX,jnyngg.cn,REJECT
DOMAIN-SUFFIX,johtzj.com,REJECT
DOMAIN-SUFFIX,jossuer.net,REJECT
DOMAIN-SUFFIX,joyfuldoors.com,REJECT
DOMAIN-SUFFIX,jp88.cc,REJECT
DOMAIN-SUFFIX,jpush.cn,REJECT
DOMAIN-SUFFIX,jqz9.com,REJECT
DOMAIN-SUFFIX,js.05sun.com,REJECT
DOMAIN-SUFFIX,js.45bubu.com,REJECT
DOMAIN-SUFFIX,js.bju888.com,REJECT
DOMAIN-SUFFIX,js.bxwns.com,REJECT
DOMAIN-SUFFIX,js.bxwxtxt.com,REJECT
DOMAIN-SUFFIX,js.cncrk.com,REJECT
DOMAIN-SUFFIX,js.duotegame.com,REJECT
DOMAIN-SUFFIX,js.hkslg520.com,REJECT
DOMAIN-SUFFIX,js.ubaike.cn,REJECT
DOMAIN-SUFFIX,js1.2abc8.com,REJECT
DOMAIN-SUFFIX,js1.xbaixing.com,REJECT
DOMAIN-SUFFIX,js1.zuocai.tv,REJECT
DOMAIN-SUFFIX,jsadt.com,REJECT
DOMAIN-SUFFIX,js-agent.newrelic.com,REJECT
DOMAIN-SUFFIX,jsb.qianzhan.com,REJECT
DOMAIN-SUFFIX,jskrnekewe.mofans.net,REJECT
DOMAIN-SUFFIX,jsm.39yst.com,REJECT
DOMAIN-SUFFIX,jsm.9939.com,REJECT
DOMAIN-SUFFIX,jsmwd.com,REJECT
DOMAIN-SUFFIX,jspg.cc,REJECT
DOMAIN-SUFFIX,jssd.uumeitu.com,REJECT
DOMAIN-SUFFIX,jtxh.net,REJECT
DOMAIN-SUFFIX,jtys8.com,REJECT
DOMAIN-SUFFIX,ju33.com,REJECT
DOMAIN-SUFFIX,juicyads.com,REJECT
DOMAIN-SUFFIX,jundazulin.com,REJECT
DOMAIN-SUFFIX,junfull.com,REJECT
DOMAIN-SUFFIX,jusha.com,REJECT
DOMAIN-SUFFIX,jutou5.com,REJECT
DOMAIN-SUFFIX,juzi.cn,REJECT
DOMAIN-SUFFIX,juzilm.com,REJECT
DOMAIN-SUFFIX,jwqj.net,REJECT
DOMAIN-SUFFIX,jxad.jx163.com,REJECT
DOMAIN-SUFFIX,jxbjt.com,REJECT
DOMAIN-SUFFIX,jxjzny.com,REJECT
DOMAIN-SUFFIX,jxlqgs.com,REJECT
DOMAIN-SUFFIX,jxxiangchu.com,REJECT
DOMAIN-SUFFIX,jystea.com,REJECT
DOMAIN-SUFFIX,jzm81.com,REJECT
DOMAIN-SUFFIX,k2team.kyiv.ua,REJECT
DOMAIN-SUFFIX,kafka8.com,REJECT
DOMAIN-SUFFIX,karma.mdpcdn.com,REJECT
DOMAIN-SUFFIX,kawa11.space,REJECT
DOMAIN-SUFFIX,kbnetworkz.s3.amazonaws.com,REJECT
DOMAIN-SUFFIX,kejet.com,REJECT
DOMAIN-SUFFIX,kejet.net,REJECT
DOMAIN-SUFFIX,kele4.com,REJECT
DOMAIN-SUFFIX,ker.pic2pic.site,REJECT
DOMAIN-SUFFIX,kermit.macnn.com,REJECT
DOMAIN-SUFFIX,keydot.net,REJECT
DOMAIN-SUFFIX,keyrun.cn,REJECT
DOMAIN-SUFFIX,keyyou.net,REJECT
DOMAIN-SUFFIX,kfluoa.com,REJECT
DOMAIN-SUFFIX,kgcjgsa8.net,REJECT
DOMAIN-SUFFIX,kguke.com,REJECT
DOMAIN-SUFFIX,kicnse.com,REJECT
DOMAIN-SUFFIX,kingwam.com,REJECT
DOMAIN-SUFFIX,kjgen.com,REJECT
DOMAIN-SUFFIX,kk7kk.com,REJECT
DOMAIN-SUFFIX,kkcaicai.com,REJECT
DOMAIN-SUFFIX,kld666.com,REJECT
DOMAIN-SUFFIX,kldmm.com,REJECT
DOMAIN-SUFFIX,klsdmr.com,REJECT
DOMAIN-SUFFIX,kmadou.com,REJECT
DOMAIN-SUFFIX,kmd365.com,REJECT
DOMAIN-SUFFIX,kmwqxqh.com,REJECT
DOMAIN-SUFFIX,knnwdyou.com,REJECT
DOMAIN-SUFFIX,kod4pc293.com,REJECT
DOMAIN-SUFFIX,koowo.com,REJECT
DOMAIN-SUFFIX,koukou7.com,REJECT
DOMAIN-SUFFIX,kqy1.com,REJECT
DOMAIN-SUFFIX,krux.net,REJECT
DOMAIN-SUFFIX,ksdsuzhou.com,REJECT
DOMAIN-SUFFIX,ksrsy.com,REJECT
DOMAIN-SUFFIX,kt220.com,REJECT
DOMAIN-SUFFIX,ktunions.com,REJECT
DOMAIN-SUFFIX,ktv0311.com,REJECT
DOMAIN-SUFFIX,ku63.com,REJECT
DOMAIN-SUFFIX,ku9377.com,REJECT
DOMAIN-SUFFIX,kuaipai666.cn,REJECT
DOMAIN-SUFFIX,kuwoyy.com,REJECT
DOMAIN-SUFFIX,kxrxh.com,REJECT
DOMAIN-SUFFIX,l.qq.com,REJECT
DOMAIN-SUFFIX,lajizhan.org,REJECT
DOMAIN-SUFFIX,langchars.com,REJECT
DOMAIN-SUFFIX,lanxiangji.com,REJECT
DOMAIN-SUFFIX,lashou1000.com,REJECT
DOMAIN-SUFFIX,le4le.com,REJECT
DOMAIN-SUFFIX,leadboltads.net,REJECT
DOMAIN-SUFFIX,leeyuoxs.com,REJECT
DOMAIN-SUFFIX,legozu.com,REJECT
DOMAIN-SUFFIX,lele999.com,REJECT
DOMAIN-SUFFIX,leztc.com,REJECT
DOMAIN-SUFFIX,lflili.com,REJECT
DOMAIN-SUFFIX,lfyuanai.com,REJECT
DOMAIN-SUFFIX,lhafy.com,REJECT
DOMAIN-SUFFIX,lhengilin.com,REJECT
DOMAIN-SUFFIX,lhusy.com,REJECT
DOMAIN-SUFFIX,liangziweixg.com,REJECT
DOMAIN-SUFFIX,lianmeng.360.cn,REJECT
DOMAIN-SUFFIX,libs.tvmao.cn,REJECT
DOMAIN-SUFFIX,life.e0575.com,REJECT
DOMAIN-SUFFIX,life.imagepix.org,REJECT
DOMAIN-SUFFIX,lightson.vpsboard.com,REJECT
DOMAIN-SUFFIX,lingdian98.com,REJECT
DOMAIN-SUFFIX,linkbide.com,REJECT
DOMAIN-SUFFIX,linkpage.cn,REJECT
DOMAIN-SUFFIX,linyao.dxsdb.com,REJECT
DOMAIN-SUFFIX,lishuanghao.com,REJECT
DOMAIN-SUFFIX,listenother.com,REJECT
DOMAIN-SUFFIX,liuliguo.com,REJECT
DOMAIN-SUFFIX,livehapp.com,REJECT
DOMAIN-SUFFIX,lixiangmo.com,REJECT
DOMAIN-SUFFIX,ll.a.hulu.com,REJECT
DOMAIN-SUFFIX,ll38.com,REJECT
DOMAIN-SUFFIX,lndjj.com,REJECT
DOMAIN-SUFFIX,lnk0.com,REJECT
DOMAIN-SUFFIX,lnk8z.com,REJECT
DOMAIN-SUFFIX,lnr2.com,REJECT
DOMAIN-SUFFIX,loandatec.com,REJECT
DOMAIN-SUFFIX,localytics.com,REJECT
DOMAIN-SUFFIX,log.interest.mix.sina.com.cn,REJECT
DOMAIN-SUFFIX,log.outbrain.com,REJECT
DOMAIN-SUFFIX,log.umsns.com,REJECT
DOMAIN-SUFFIX,logs.dashlane.com,REJECT
DOMAIN-SUFFIX,lomark.cn,REJECT
DOMAIN-SUFFIX,londonprivaterentals.standard.co.uk,REJECT
DOMAIN-SUFFIX,looky.hyves.org,REJECT
DOMAIN-SUFFIX,lotuseed.com,REJECT
DOMAIN-SUFFIX,lovestyl.com,REJECT
DOMAIN-SUFFIX,lqmohun.com,REJECT
DOMAIN-SUFFIX,lrswl.com,REJECT
DOMAIN-SUFFIX,ls.webmd.com,REJECT
DOMAIN-SUFFIX,lsxmg.com,REJECT
DOMAIN-SUFFIX,ltcprtc.com,REJECT
DOMAIN-SUFFIX,lthxz.cn,REJECT
DOMAIN-SUFFIX,lubosheng.cn,REJECT
DOMAIN-SUFFIX,lucting.cn,REJECT
DOMAIN-SUFFIX,lufax.com,REJECT
DOMAIN-SUFFIX,lw1.cdmediaworld.com,REJECT
DOMAIN-SUFFIX,lw2.gamecopyworld.com,REJECT
DOMAIN-SUFFIX,lx167.com,REJECT
DOMAIN-SUFFIX,lxqcgj.com,REJECT
DOMAIN-SUFFIX,lxting.com,REJECT
DOMAIN-SUFFIX,lyaeccn.com,REJECT
DOMAIN-SUFFIX,lyhdream.com,REJECT
DOMAIN-SUFFIX,lyrymy.com,REJECT
DOMAIN-SUFFIX,lytubaobao.com,REJECT
DOMAIN-SUFFIX,lzjycy.com,REJECT
DOMAIN-SUFFIX,lzmm8.com,REJECT
DOMAIN-SUFFIX,m.simaba.taobao.com,REJECT
DOMAIN-SUFFIX,ma1.meishij.net,REJECT
DOMAIN-SUFFIX,madhouse.cn,REJECT
DOMAIN-SUFFIX,madmini.com,REJECT
DOMAIN-SUFFIX,mads.amazon.com,REJECT
DOMAIN-SUFFIX,mads.aol.com,REJECT
DOMAIN-SUFFIX,mads.dailymail.co.uk,REJECT
DOMAIN-SUFFIX,magicwindow.cn,REJECT
DOMAIN-SUFFIX,maibahe300cc.com,REJECT
DOMAIN-SUFFIX,mainbx.com,REJECT
DOMAIN-SUFFIX,maisoncherry.com,REJECT
DOMAIN-SUFFIX,manads.static.olcdn.com,REJECT
DOMAIN-SUFFIX,manage.wdfans.cn,REJECT
DOMAIN-SUFFIX,maomaotang.com,REJECT
DOMAIN-SUFFIX,market.178.com,REJECT
DOMAIN-SUFFIX,market.21cn.com,REJECT
DOMAIN-SUFFIX,market.aliyun.com,REJECT
DOMAIN-SUFFIX,market.duowan.com,REJECT
DOMAIN-SUFFIX,marketgid.com,REJECT
DOMAIN-SUFFIX,marketing.888.com,REJECT
DOMAIN-SUFFIX,marketingsolutions.yahoo.com,REJECT
DOMAIN-SUFFIX,maskbaby.com.cn,REJECT
DOMAIN-SUFFIX,mathtag.com,REJECT
DOMAIN-SUFFIX,maysunmedia.com,REJECT
DOMAIN-SUFFIX,mb.hockeybuzz.com,REJECT
DOMAIN-SUFFIX,mb.zam.com,REJECT
DOMAIN-SUFFIX,mbs.weathercn.com,REJECT
DOMAIN-SUFFIX,md0z4dh.com,REJECT
DOMAIN-SUFFIX,mealsandsteals.sandiego6.com,REJECT
DOMAIN-SUFFIX,media8.cn,REJECT
DOMAIN-SUFFIX,mediamgr.ugo.com,REJECT
DOMAIN-SUFFIX,mediaplex.com,REJECT
DOMAIN-SUFFIX,mediav.com,REJECT
DOMAIN-SUFFIX,medrx.telstra.com.au,REJECT
DOMAIN-SUFFIX,megajoy.com,REJECT
DOMAIN-SUFFIX,meimeidaren.com,REJECT
DOMAIN-SUFFIX,meiti1.net,REJECT
DOMAIN-SUFFIX,menghuanzs.com,REJECT
DOMAIN-SUFFIX,mengmengdas.com,REJECT
DOMAIN-SUFFIX,mg.5pk,REJECT
DOMAIN-SUFFIX,mgid.com,REJECT
DOMAIN-SUFFIX,mgogo.com,REJECT
DOMAIN-SUFFIX,mgwcn.com,REJECT
DOMAIN-SUFFIX,mgwl668.com,REJECT
DOMAIN-SUFFIX,mi.gdt.qq.com,REJECT
DOMAIN-SUFFIX,miaozhen.com,REJECT
DOMAIN-SUFFIX,microad-cn.com,REJECT
DOMAIN-SUFFIX,miidi.net,REJECT
DOMAIN-SUFFIX,mijifen.com,REJECT
DOMAIN-SUFFIX,milk.yesky.com.cn,REJECT
DOMAIN-SUFFIX,mingxianshanghang.cn,REJECT
DOMAIN-SUFFIX,mingysh.com,REJECT
DOMAIN-SUFFIX,mini.hao123.com,REJECT
DOMAIN-SUFFIX,miniye.xjts.cn,REJECT
DOMAIN-SUFFIX,mixpanel.com,REJECT
DOMAIN-SUFFIX,mm.anqu.com,REJECT
DOMAIN-SUFFIX,mmcc.yxlady.com,REJECT
DOMAIN-SUFFIX,mng-ads.com,REJECT
DOMAIN-SUFFIX,mnkan.com,REJECT
DOMAIN-SUFFIX,mnwan.com,REJECT
DOMAIN-SUFFIX,mnxtu.com,REJECT
DOMAIN-SUFFIX,moad.cn,REJECT
DOMAIN-SUFFIX,moatads.com,REJECT
DOMAIN-SUFFIX,mobaders.com,REJECT
DOMAIN-SUFFIX,mobadme.jp,REJECT
DOMAIN-SUFFIX,mobclix.com,REJECT
DOMAIN-SUFFIX,mobfox.com,REJECT
DOMAIN-SUFFIX,mobgi.com,REJECT
DOMAIN-SUFFIX,mobileads.msn.com,REJECT
DOMAIN-SUFFIX,mobileapptracking.com,REJECT
DOMAIN-SUFFIX,mobilityware.com,REJECT
DOMAIN-SUFFIX,mobiorg8.com,REJECT
DOMAIN-SUFFIX,mobisage.cn,REJECT
DOMAIN-SUFFIX,mobvista.com,REJECT
DOMAIN-SUFFIX,money.qz828.com,REJECT
DOMAIN-SUFFIX,monitor.uu.qq.com,REJECT
DOMAIN-SUFFIX,momo.popupad.cn,REJECT
DOMAIN-SUFFIX,moodoocrv.com.cn,REJECT
DOMAIN-SUFFIX,mookie1.com,REJECT
DOMAIN-SUFFIX,moonwish.com.cn,REJECT
DOMAIN-SUFFIX,mopub.com,REJECT
DOMAIN-SUFFIX,moquanad.com,REJECT
DOMAIN-SUFFIX,mosa86.com,REJECT
DOMAIN-SUFFIX,motohelpr.com,REJECT
DOMAIN-SUFFIX,moutaihotel.cn,REJECT
DOMAIN-SUFFIX,mpb1.iteye.com,REJECT
DOMAIN-SUFFIX,mps.nbcuni.com,REJECT
DOMAIN-SUFFIX,mpush.cn,REJECT
DOMAIN-SUFFIX,mrksys.com,REJECT
DOMAIN-SUFFIX,msads.net,REJECT
DOMAIN-SUFFIX,mscimg.com,REJECT
DOMAIN-SUFFIX,msga.71.am,REJECT
DOMAIN-SUFFIX,msltzer.cn,REJECT
DOMAIN-SUFFIX,mstzym.com,REJECT
DOMAIN-SUFFIX,msypr.com,REJECT
DOMAIN-SUFFIX,mtj.baidu.com,REJECT
DOMAIN-SUFFIX,mtxsk.com,REJECT
DOMAIN-SUFFIX,mwlucuvbyrff.com,REJECT
DOMAIN-SUFFIX,mxmrt.com,REJECT
DOMAIN-SUFFIX,mxpnl.com,REJECT
DOMAIN-SUFFIX,myad.toocle.com,REJECT
DOMAIN-SUFFIX,mydas.mobi,REJECT
DOMAIN-SUFFIX,mytanwan.com,REJECT
DOMAIN-SUFFIX,mytzdhz.cn,REJECT
DOMAIN-SUFFIX,myycrw.com,REJECT
DOMAIN-SUFFIX,myzk1.com,REJECT
DOMAIN-SUFFIX,myzwqwe12.com,REJECT
DOMAIN-SUFFIX,mzy2014.com,REJECT
DOMAIN-SUFFIX,n.cosbot.cn,REJECT
DOMAIN-SUFFIX,namedq.com,REJECT
DOMAIN-SUFFIX,namemek.com,REJECT
DOMAIN-SUFFIX,naqigs.com,REJECT
DOMAIN-SUFFIX,nbhxgjz.com,REJECT
DOMAIN-SUFFIX,nbjjd.com,REJECT
DOMAIN-SUFFIX,nbzq.net,REJECT
DOMAIN-SUFFIX,ncachear.com,REJECT
DOMAIN-SUFFIX,nchte.com,REJECT
DOMAIN-SUFFIX,ndtzx.com,REJECT
DOMAIN-SUFFIX,ne9377.com,REJECT
DOMAIN-SUFFIX,nend.net,REJECT
DOMAIN-SUFFIX,nest.youwatch.org,REJECT
DOMAIN-SUFFIX,netko0o.com,REJECT
DOMAIN-SUFFIX,netshelter.net,REJECT
DOMAIN-SUFFIX,netspidermm.indiatimes.com,REJECT
DOMAIN-SUFFIX,network.aufeminin.com,REJECT
DOMAIN-SUFFIX,network.business.com,REJECT
DOMAIN-SUFFIX,network.sofeminine.co.uk,REJECT
DOMAIN-SUFFIX,networkbench.com,REJECT
DOMAIN-SUFFIX,new.zhqiu.com,REJECT
DOMAIN-SUFFIX,newrelic.com,REJECT
DOMAIN-SUFFIX,nexage.com,REJECT
DOMAIN-SUFFIX,nextcps.com,REJECT
DOMAIN-SUFFIX,ninebox.cn,REJECT
DOMAIN-SUFFIX,niurenw.com,REJECT
DOMAIN-SUFFIX,niux88.com,REJECT
DOMAIN-SUFFIX,niuxgame77.com,REJECT
DOMAIN-SUFFIX,njdijiani.com,REJECT
DOMAIN-SUFFIX,njfsk.com,REJECT
DOMAIN-SUFFIX,njq.net,REJECT
DOMAIN-SUFFIX,nkeo.top,REJECT
DOMAIN-SUFFIX,nmkgs.cn,REJECT
DOMAIN-SUFFIX,nmpcdn.com,REJECT
DOMAIN-SUFFIX,nmqbg.com,REJECT
DOMAIN-SUFFIX,nnedbx.com,REJECT
DOMAIN-SUFFIX,nngft.com,REJECT
DOMAIN-SUFFIX,noberlmall.com,REJECT
DOMAIN-SUFFIX,nongsalei.com,REJECT
DOMAIN-SUFFIX,notice.uchome.manyou.com,REJECT
DOMAIN-SUFFIX,nowskip.com,REJECT
DOMAIN-SUFFIX,nr1234.com,REJECT
DOMAIN-SUFFIX,nsnmiaomu.cn,REJECT
DOMAIN-SUFFIX,ntalker.com,REJECT
DOMAIN-SUFFIX,nterbx.com,REJECT
DOMAIN-SUFFIX,nthyn.com,REJECT
DOMAIN-SUFFIX,nwwap.com,REJECT
DOMAIN-SUFFIX,nxrhs.com,REJECT
DOMAIN-SUFFIX,nylalobghyhirgh.com,REJECT
DOMAIN-SUFFIX,nysita.com,REJECT
DOMAIN-SUFFIX,nzezn.com,REJECT
DOMAIN-SUFFIX,o091i.com,REJECT
DOMAIN-SUFFIX,o2omobi.com,REJECT
DOMAIN-SUFFIX,o7xs6runw.bkt.clouddn.com,REJECT
DOMAIN-SUFFIX,oa129.com,REJECT
DOMAIN-SUFFIX,oadz.com,REJECT
DOMAIN-SUFFIX,oas.autotrader.co.uk,REJECT
DOMAIN-SUFFIX,oas.luxweb.com,REJECT
DOMAIN-SUFFIX,oas.skyscanner.net,REJECT
DOMAIN-SUFFIX,oasc07.citywire.co.uk,REJECT
DOMAIN-SUFFIX,oascentral.chron.com,REJECT
DOMAIN-SUFFIX,oascentral.hosted.ap.org,REJECT
DOMAIN-SUFFIX,oascentral.newsmax.com,REJECT
DOMAIN-SUFFIX,ocsp.godaddy.com,REJECT
DOMAIN-SUFFIX,odin.goo.mx,REJECT
DOMAIN-SUFFIX,officeme.cn,REJECT
DOMAIN-SUFFIX,oikxlcv.wang,REJECT
DOMAIN-SUFFIX,ok.432kkk.com,REJECT
DOMAIN-SUFFIX,okkkk.com,REJECT
DOMAIN-SUFFIX,okm918.com,REJECT
DOMAIN-SUFFIX,okokw.com,REJECT
DOMAIN-SUFFIX,olcdn.com,REJECT
DOMAIN-SUFFIX,on.maxspeedcdn.com,REJECT
DOMAIN-SUFFIX,onclickads.net,REJECT
DOMAIN-SUFFIX,one.520319.cn,REJECT
DOMAIN-SUFFIX,oneapm.com,REJECT
DOMAIN-SUFFIX,onetad.com,REJECT
DOMAIN-SUFFIX,onlifjj.net,REJECT
DOMAIN-SUFFIX,oomyv.com,REJECT
DOMAIN-SUFFIX,ooss.oss.aliyuncs.com,REJECT
DOMAIN-SUFFIX,optaim.com,REJECT
DOMAIN-SUFFIX,optimix.asia,REJECT
DOMAIN-SUFFIX,optimizelyapis.com,REJECT
DOMAIN-SUFFIX,oq68.com,REJECT
DOMAIN-SUFFIX,orchidscape.net,REJECT
DOMAIN-SUFFIX,orz.hupu.com,REJECT
DOMAIN-SUFFIX,ou188.com,REJECT
DOMAIN-SUFFIX,overture.com,REJECT
DOMAIN-SUFFIX,ox.furaffinity.net,REJECT
DOMAIN-SUFFIX,p.7060.la,REJECT
DOMAIN-SUFFIX,p.ecwan77.net,REJECT
DOMAIN-SUFFIX,p.szonline.net,REJECT
DOMAIN-SUFFIX,p0y.cn,REJECT
DOMAIN-SUFFIX,p1.18zhongyao.com,REJECT
DOMAIN-SUFFIX,p3tt.com,REJECT
DOMAIN-SUFFIX,p4p.sina.com.cn,REJECT
DOMAIN-SUFFIX,p8u.hinet.net,REJECT
DOMAIN-SUFFIX,page.amap.com,REJECT
DOMAIN-SUFFIX,pagechoice.com,REJECT
DOMAIN-SUFFIX,painiuimg.com,REJECT
DOMAIN-SUFFIX,panda.kdnet.net,REJECT
DOMAIN-SUFFIX,pangu.cc,REJECT
DOMAIN-SUFFIX,papajia55.com,REJECT
DOMAIN-SUFFIX,partner.bargaindomains.com,REJECT
DOMAIN-SUFFIX,partner.catchy.com,REJECT
DOMAIN-SUFFIX,partner.premiumdomains.com,REJECT
DOMAIN-SUFFIX,partners.fshealth.com,REJECT
DOMAIN-SUFFIX,partners.keezmovies.com,REJECT
DOMAIN-SUFFIX,partners.optiontide.com,REJECT
DOMAIN-SUFFIX,partners.pornerbros.com,REJECT
DOMAIN-SUFFIX,partners.rochen.com,REJECT
DOMAIN-SUFFIX,partners.sportingbet.com.au,REJECT
DOMAIN-SUFFIX,partners.vouchedfor.co.uk,REJECT
DOMAIN-SUFFIX,partners.xpertmarket.com,REJECT
DOMAIN-SUFFIX,passwz.com,REJECT
DOMAIN-SUFFIX,pay838.com,REJECT
DOMAIN-SUFFIX,pb.s3wfg.com,REJECT
DOMAIN-SUFFIX,pcmzn.com,REJECT
DOMAIN-SUFFIX,pdsjycm.com,REJECT
DOMAIN-SUFFIX,pedailyu.com,REJECT
DOMAIN-SUFFIX,pee.cn,REJECT
DOMAIN-SUFFIX,pfp.sina.com.cn,REJECT
DOMAIN-SUFFIX,phpad.cqnews.net,REJECT
DOMAIN-SUFFIX,pic.0597kk.com,REJECT
DOMAIN-SUFFIX,pic.111cn.net,REJECT
DOMAIN-SUFFIX,pic.2u.com.cn,REJECT
DOMAIN-SUFFIX,pic.ea3w.com,REJECT
DOMAIN-SUFFIX,pic.fengniao.com,REJECT
DOMAIN-SUFFIX,picsinfog.com,REJECT
DOMAIN-SUFFIX,pingbi.diudou.com,REJECT
DOMAIN-SUFFIX,pingdom.net,REJECT
DOMAIN-SUFFIX,pingjs.qq.com,REJECT
DOMAIN-SUFFIX,pingshetrip.com,REJECT
DOMAIN-SUFFIX,pixel.rubiconproject.com,REJECT
DOMAIN-SUFFIX,pixel.wp.com,REJECT
DOMAIN-SUFFIX,pixfuture.net,REJECT
DOMAIN-SUFFIX,pjtymy.cn,REJECT
DOMAIN-SUFFIX,pk840.com,REJECT
DOMAIN-SUFFIX,playad.xjmg.com,REJECT
DOMAIN-SUFFIX,player.1800coupon.com,REJECT
DOMAIN-SUFFIX,player.1stcreditrepairs.com,REJECT
DOMAIN-SUFFIX,player.800directories.com,REJECT
DOMAIN-SUFFIX,player.accoona.com,REJECT
DOMAIN-SUFFIX,player.alloutwedding.com,REJECT
DOMAIN-SUFFIX,player.insuranceandhealth.com,REJECT
DOMAIN-SUFFIX,plwan.com,REJECT
DOMAIN-SUFFIX,pmm.people.com.cn,REJECT
DOMAIN-SUFFIX,pnhfc.com,REJECT
DOMAIN-SUFFIX,pofang.com,REJECT
DOMAIN-SUFFIX,polkoa.com,REJECT
DOMAIN-SUFFIX,pomhz.com,REJECT
DOMAIN-SUFFIX,pop.91mangrandi.com,REJECT
DOMAIN-SUFFIX,pos.baidu.com,REJECT
DOMAIN-SUFFIX,poster.weather.com.cn,REJECT
DOMAIN-SUFFIX,powergg.top,REJECT
DOMAIN-SUFFIX,poyang.com,REJECT
DOMAIN-SUFFIX,ppjia55.com,REJECT
DOMAIN-SUFFIX,pr00001.com,REJECT
DOMAIN-SUFFIX,prerollads.ign.com,REJECT
DOMAIN-SUFFIX,priceinfo.comuv.com,REJECT
DOMAIN-SUFFIX,pro.cn,REJECT
DOMAIN-SUFFIX,pro.iweihai.cn,REJECT
DOMAIN-SUFFIX,probes.cedexis.com,REJECT
DOMAIN-SUFFIX,projectwonderful.com,REJECT
DOMAIN-SUFFIX,promo.fileforum.com,REJECT
DOMAIN-SUFFIX,promos.fling.com,REJECT
DOMAIN-SUFFIX,promote.pair.com,REJECT
DOMAIN-SUFFIX,promotions.iasbet.com,REJECT
DOMAIN-SUFFIX,propellerads.com,REJECT
DOMAIN-SUFFIX,prophet.heise.de,REJECT
DOMAIN-SUFFIX,ptdrw.com,REJECT
DOMAIN-SUFFIX,ptkhy.com,REJECT
DOMAIN-SUFFIX,ptw.la,REJECT
DOMAIN-SUFFIX,pub.betclick.com,REJECT
DOMAIN-SUFFIX,pub.chinadailyasia.com,REJECT
DOMAIN-SUFFIX,pub.funshion.com,REJECT
DOMAIN-SUFFIX,pub1.cope.es,REJECT
DOMAIN-SUFFIX,pubbirdf.com,REJECT
DOMAIN-SUFFIX,public6.com,REJECT
DOMAIN-SUFFIX,publicidad.net,REJECT
DOMAIN-SUFFIX,publicidad.tv,REJECT
DOMAIN-SUFFIX,publish.ad.youth.cn,REJECT
DOMAIN-SUFFIX,pubmatic.com,REJECT
DOMAIN-SUFFIX,pubnub.com,REJECT
DOMAIN-SUFFIX,pubs.hiddennetwork.com,REJECT
DOMAIN-SUFFIX,pups.bdimg.com,REJECT
DOMAIN-SUFFIX,push.air-matters.com,REJECT
DOMAIN-SUFFIX,pyzkk.com,REJECT
DOMAIN-SUFFIX,qbyy010.com,REJECT
DOMAIN-SUFFIX,qcjslm.com,REJECT
DOMAIN-SUFFIX,qcl777.com,REJECT
DOMAIN-SUFFIX,qd.dhzw.org,REJECT
DOMAIN-SUFFIX,qd.js.sanjiangge.com,REJECT
DOMAIN-SUFFIX,qd.wanjuanba.com,REJECT
DOMAIN-SUFFIX,qd.x4399.com,REJECT
DOMAIN-SUFFIX,qdchunyu.com,REJECT
DOMAIN-SUFFIX,qgss8.com,REJECT
DOMAIN-SUFFIX,qhaif.com,REJECT
DOMAIN-SUFFIX,qiailm.com,REJECT
DOMAIN-SUFFIX,qihaoqu.com,REJECT
DOMAIN-SUFFIX,qingqu.la,REJECT
DOMAIN-SUFFIX,qiqipower.com,REJECT
DOMAIN-SUFFIX,qiqivv.com,REJECT
DOMAIN-SUFFIX,qiqiww.com,REJECT
DOMAIN-SUFFIX,qiqiyii.com,REJECT
DOMAIN-SUFFIX,qiyou.com,REJECT
DOMAIN-SUFFIX,qjjtc.com,REJECT
DOMAIN-SUFFIX,qloer.com,REJECT
DOMAIN-SUFFIX,qlonglong.com,REJECT
DOMAIN-SUFFIX,qmkdy.com,REJECT
DOMAIN-SUFFIX,qoiusky.com,REJECT
DOMAIN-SUFFIX,qooic.com,REJECT
DOMAIN-SUFFIX,qq2.co,REJECT
DOMAIN-SUFFIX,qq61.com,REJECT
DOMAIN-SUFFIX,qqhuhu.com,REJECT
DOMAIN-SUFFIX,qqm98.com,REJECT
DOMAIN-SUFFIX,qqzu.com,REJECT
DOMAIN-SUFFIX,qsbz2011.com,REJECT
DOMAIN-SUFFIX,qshxc.com,REJECT
DOMAIN-SUFFIX,qt.biqugezw.com,REJECT
DOMAIN-SUFFIX,qtmojo.cn,REJECT
DOMAIN-SUFFIX,qtmojo.com,REJECT
DOMAIN-SUFFIX,quanliyouxi.cn,REJECT
DOMAIN-SUFFIX,quansj.cn,REJECT
DOMAIN-SUFFIX,quantcount.com,REJECT
DOMAIN-SUFFIX,quantserve.com,REJECT
DOMAIN-SUFFIX,qucaigg.com,REJECT
DOMAIN-SUFFIX,queene.cn,REJECT
DOMAIN-SUFFIX,questionmarket.com,REJECT
DOMAIN-SUFFIX,qujishu.com,REJECT
DOMAIN-SUFFIX,qumi.com,REJECT
DOMAIN-SUFFIX,quw18.com,REJECT
DOMAIN-SUFFIX,qxjdlf.com,REJECT
DOMAIN-SUFFIX,qxxys.com,REJECT
DOMAIN-SUFFIX,qyctj.com,REJECT
DOMAIN-SUFFIX,qytyf.com,REJECT
DOMAIN-SUFFIX,qzdag.com,REJECT
DOMAIN-SUFFIX,qzdfc.com,REJECT
DOMAIN-SUFFIX,qzkxt.com,REJECT
DOMAIN-SUFFIX,r.radikal.ru,REJECT
DOMAIN-SUFFIX,rack.bauermedia.co.uk,REJECT
DOMAIN-SUFFIX,rad.microsoft.com,REJECT
DOMAIN-SUFFIX,rad.msn.com,REJECT
DOMAIN-SUFFIX,radar.cedexis.com,REJECT
DOMAIN-SUFFIX,rbywg.com,REJECT
DOMAIN-SUFFIX,rdiqt.cn,REJECT
DOMAIN-SUFFIX,re.taotaosou.com,REJECT
DOMAIN-SUFFIX,reachmax.cn,REJECT
DOMAIN-SUFFIX,realtime.monitor.ppweb.com.cn,REJECT
DOMAIN-SUFFIX,red.bayimg.net,REJECT
DOMAIN-SUFFIX,redvase.bravenet.com,REJECT
DOMAIN-SUFFIX,reliancevalve.com,REJECT
DOMAIN-SUFFIX,remotedu.cn,REJECT
DOMAIN-SUFFIX,rencai56.com,REJECT
DOMAIN-SUFFIX,res.hunantv.com,REJECT
DOMAIN-SUFFIX,res3.feedsportal.com,REJECT
DOMAIN-SUFFIX,resetgey.com,REJECT
DOMAIN-SUFFIX,resource.baomihua.com,REJECT
DOMAIN-SUFFIX,responsys.net,REJECT
DOMAIN-SUFFIX,rev.fapdu.com,REJECT
DOMAIN-SUFFIX,revdepo.com,REJECT
DOMAIN-SUFFIX,revealads.appspot.com,REJECT
DOMAIN-SUFFIX,revsci.net,REJECT
DOMAIN-SUFFIX,rh.qq.com,REJECT
DOMAIN-SUFFIX,rhgyg.com,REJECT
DOMAIN-SUFFIX,richmedia.yimg.com,REJECT
DOMAIN-SUFFIX,riqu2015.com,REJECT
DOMAIN-SUFFIX,rlcdn.com,REJECT
DOMAIN-SUFFIX,rmcxw.cn,REJECT
DOMAIN-SUFFIX,rnfrfxqztlno.com,REJECT
DOMAIN-SUFFIX,roia.com,REJECT
DOMAIN-SUFFIX,rotabanner.kulichki.net,REJECT
DOMAIN-SUFFIX,rotator.tradetracker.net,REJECT
DOMAIN-SUFFIX,router.bittorrent.com,REJECT
DOMAIN-SUFFIX,rpaulfrank.com,REJECT
DOMAIN-SUFFIX,rpt.anchorfree.net,REJECT
DOMAIN-SUFFIX,rqgsf.com,REJECT
DOMAIN-SUFFIX,rrsubway.com,REJECT
DOMAIN-SUFFIX,rs1.rensheng5.com,REJECT
DOMAIN-SUFFIX,rsccs.com,REJECT
DOMAIN-SUFFIX,rt.applovin.com,REJECT
DOMAIN-SUFFIX,rtb.eanalyzer.de,REJECT
DOMAIN-SUFFIX,rtb.metrigo.com,REJECT
DOMAIN-SUFFIX,rtbasia.com,REJECT
DOMAIN-SUFFIX,ruan88.com,REJECT
DOMAIN-SUFFIX,runetki.joyreactor.ru,REJECT
DOMAIN-SUFFIX,runiman.com,REJECT
DOMAIN-SUFFIX,ruxianke.com,REJECT
DOMAIN-SUFFIX,rwjfs.com,REJECT
DOMAIN-SUFFIX,rxwan.com,REJECT
DOMAIN-SUFFIX,s.35kds.com,REJECT
DOMAIN-SUFFIX,s.6travel.com,REJECT
DOMAIN-SUFFIX,s.abcache.com,REJECT
DOMAIN-SUFFIX,s.zixuntop.com,REJECT
DOMAIN-SUFFIX,s1.hiapk.com,REJECT
DOMAIN-SUFFIX,s1.qiuyi.cn,REJECT
DOMAIN-SUFFIX,s400cc.com,REJECT
DOMAIN-SUFFIX,s8x1.com,REJECT
DOMAIN-SUFFIX,s9w.cc,REJECT
DOMAIN-SUFFIX,sad.qeo.cn,REJECT
DOMAIN-SUFFIX,saferwet.com,REJECT
DOMAIN-SUFFIX,same.chinadaily.com.cn,REJECT
DOMAIN-SUFFIX,same.eastmoney.com,REJECT
DOMAIN-SUFFIX,same.jrj.com.cn,REJECT
DOMAIN-SUFFIX,sangxi.top,REJECT
DOMAIN-SUFFIX,sape.ru,REJECT
DOMAIN-SUFFIX,sbrqp.com,REJECT
DOMAIN-SUFFIX,sc1369.com,REJECT
DOMAIN-SUFFIX,sciencelolb.com,REJECT
DOMAIN-SUFFIX,sclizhong.com,REJECT
DOMAIN-SUFFIX,scorecardresearch.com,REJECT
DOMAIN-SUFFIX,script.vccoo.com,REJECT
DOMAIN-SUFFIX,scriptcc.cc,REJECT
DOMAIN-SUFFIX,scupio.com,REJECT
DOMAIN-SUFFIX,scw0.com,REJECT
DOMAIN-SUFFIX,sdhzstone.net,REJECT
DOMAIN-SUFFIX,sdkdm.com,REJECT
DOMAIN-SUFFIX,sdqoi2d.com,REJECT
DOMAIN-SUFFIX,sdwfw.com,REJECT
DOMAIN-SUFFIX,sdycd.com,REJECT
DOMAIN-SUFFIX,searchignited.com,REJECT
DOMAIN-SUFFIX,sebar.thand.info,REJECT
DOMAIN-SUFFIX,secretmedia.s3.amazonaws.com,REJECT
DOMAIN-SUFFIX,segment.com,REJECT
DOMAIN-SUFFIX,sell1.etlong.com,REJECT
DOMAIN-SUFFIX,serrano.hardwareheaven.com,REJECT
DOMAIN-SUFFIX,servedby.keygamesnetwork.com,REJECT
DOMAIN-SUFFIX,serving-sys.com,REJECT
DOMAIN-SUFFIX,sfloushi.com,REJECT
DOMAIN-SUFFIX,sgbfjs.info,REJECT
DOMAIN-SUFFIX,sgg.southcn.com,REJECT
DOMAIN-SUFFIX,shaft.jebe.renren.com,REJECT
DOMAIN-SUFFIX,shama5.com,REJECT
DOMAIN-SUFFIX,shanghaironghua.com,REJECT
DOMAIN-SUFFIX,shanglinli.com,REJECT
DOMAIN-SUFFIX,share.gzdsw.com,REJECT
DOMAIN-SUFFIX,sharedaddomain.com,REJECT
DOMAIN-SUFFIX,sharrysweb.com,REJECT
DOMAIN-SUFFIX,shbywsd.cn,REJECT
DOMAIN-SUFFIX,shenleyuni.com,REJECT
DOMAIN-SUFFIX,shenyian.net,REJECT
DOMAIN-SUFFIX,shiftrro.com,REJECT
DOMAIN-SUFFIX,shili.downxia.com,REJECT
DOMAIN-SUFFIX,shili.wanyx.com,REJECT
DOMAIN-SUFFIX,shop265.com,REJECT
DOMAIN-SUFFIX,shoppingpartners2.futurenet.com,REJECT
DOMAIN-SUFFIX,show.kc.taotaosou.com,REJECT
DOMAIN-SUFFIX,show.qx15.com,REJECT
DOMAIN-SUFFIX,show.xiazai16.com,REJECT
DOMAIN-SUFFIX,showcase.vpsboard.com,REJECT
DOMAIN-SUFFIX,showing.hardwareheaven.com,REJECT
DOMAIN-SUFFIX,shows.21cn.com,REJECT
DOMAIN-SUFFIX,shucaihangjia.com,REJECT
DOMAIN-SUFFIX,shuiguo.com,REJECT
DOMAIN-SUFFIX,shushijiameng123.com,REJECT
DOMAIN-SUFFIX,shuttle.bayescom.com,REJECT
DOMAIN-SUFFIX,shuzilm.cn,REJECT
DOMAIN-SUFFIX,shxinjie.cn,REJECT
DOMAIN-SUFFIX,si9377.com,REJECT
DOMAIN-SUFFIX,sicentlife.com,REJECT
DOMAIN-SUFFIX,sigbusa.com,REJECT
DOMAIN-SUFFIX,sinaalicdn.com,REJECT
DOMAIN-SUFFIX,sinaaliyun.cn,REJECT
DOMAIN-SUFFIX,site.img.4tube.com,REJECT
DOMAIN-SUFFIX,sitemeter.com,REJECT
DOMAIN-SUFFIX,sitemobia.com,REJECT
DOMAIN-SUFFIX,sitescout.com,REJECT
DOMAIN-SUFFIX,sitetag.us,REJECT
DOMAIN-SUFFIX,skatehot.net,REJECT
DOMAIN-SUFFIX,slot.union.ucweb.com,REJECT
DOMAIN-SUFFIX,smartadserver.com,REJECT
DOMAIN-SUFFIX,smartmad.com,REJECT
DOMAIN-SUFFIX,smblock.s3.amazonaws.com,REJECT
DOMAIN-SUFFIX,smxay.com,REJECT
DOMAIN-SUFFIX,smxsg.com,REJECT
DOMAIN-SUFFIX,snnnyy.com,REJECT
DOMAIN-SUFFIX,so9l.com,REJECT
DOMAIN-SUFFIX,socdm.com,REJECT
DOMAIN-SUFFIX,social-touch.com,REJECT
DOMAIN-SUFFIX,somecoding.com,REJECT
DOMAIN-SUFFIX,sonomoyo.com,REJECT
DOMAIN-SUFFIX,sos0easy.com,REJECT
DOMAIN-SUFFIX,spade.twitch.tv,REJECT
DOMAIN-SUFFIX,sphwq.net,REJECT
DOMAIN-SUFFIX,sponsorpay.com,REJECT
DOMAIN-SUFFIX,sponsors.s2ki.com,REJECT
DOMAIN-SUFFIX,sponsors.webosroundup.com,REJECT
DOMAIN-SUFFIX,spproxy.autobytel.com,REJECT
DOMAIN-SUFFIX,spt.dictionary.com,REJECT
DOMAIN-SUFFIX,sqext.com,REJECT
DOMAIN-SUFFIX,squarespace.evyy.net,REJECT
DOMAIN-SUFFIX,srv.thespacereporter.com,REJECT
DOMAIN-SUFFIX,ss.shicimingju.com,REJECT
DOMAIN-SUFFIX,sscefsol.com,REJECT
DOMAIN-SUFFIX,ssdaili.com,REJECT
DOMAIN-SUFFIX,ssjpx.com,REJECT
DOMAIN-SUFFIX,ssjy168.com,REJECT
DOMAIN-SUFFIX,ssp.kssws.ks-cdn.com,REJECT
DOMAIN-SUFFIX,ssp.zf313.com,REJECT
DOMAIN-SUFFIX,sspapi.youxiaoad.com,REJECT
DOMAIN-SUFFIX,sss.sege.xxx,REJECT
DOMAIN-SUFFIX,sssvd.china.com,REJECT
DOMAIN-SUFFIX,sstc360.com,REJECT
DOMAIN-SUFFIX,staqnet.com,REJECT
DOMAIN-SUFFIX,star8.net,REJECT
DOMAIN-SUFFIX,static.ichehome.com,REJECT
DOMAIN-SUFFIX,static.kinghost.com,REJECT
DOMAIN-SUFFIX,static.oh100.com,REJECT
DOMAIN-SUFFIX,static.plista.com,REJECT
DOMAIN-SUFFIX,static.tucsonsentinel.com,REJECT
DOMAIN-SUFFIX,static-xl9-ssl.xunlei.com,REJECT
DOMAIN-SUFFIX,stats.chinaz.com,REJECT
DOMAIN-SUFFIX,stats.developingperspective.com,REJECT
DOMAIN-SUFFIX,stats.hosting24.com,REJECT
DOMAIN-SUFFIX,stats.jpush.cn,REJECT
DOMAIN-SUFFIX,stats.sitesuite.org,REJECT
DOMAIN-SUFFIX,stervapoimenialena.info,REJECT
DOMAIN-SUFFIX,stg8.com,REJECT
DOMAIN-SUFFIX,stn88.com,REJECT
DOMAIN-SUFFIX,stocksbsc.com,REJECT
DOMAIN-SUFFIX,storewidget.pcauthority.com.au,REJECT
DOMAIN-SUFFIX,stream.heavenmedia.net,REJECT
DOMAIN-SUFFIX,streaming.rtbiddingplatform.com,REJECT
DOMAIN-SUFFIX,sub.powerapple.com,REJECT
DOMAIN-SUFFIX,sub.topber.com,REJECT
DOMAIN-SUFFIX,sucodb.com,REJECT
DOMAIN-SUFFIX,sunjianhao.com,REJECT
DOMAIN-SUFFIX,super.cat898.com,REJECT
DOMAIN-SUFFIX,super.kdnet.net,REJECT
DOMAIN-SUFFIX,surv.xbizmedia.com,REJECT
DOMAIN-SUFFIX,survey.g.doubleclick.net,REJECT
DOMAIN-SUFFIX,swa.gtimg.com,REJECT
DOMAIN-SUFFIX,switchadhub.com,REJECT
DOMAIN-SUFFIX,sxbhzs.net,REJECT
DOMAIN-SUFFIX,sxdyrq.com,REJECT
DOMAIN-SUFFIX,sxjxhg.com,REJECT
DOMAIN-SUFFIX,sxz67.com,REJECT
DOMAIN-SUFFIX,sycbbs.com,REJECT
DOMAIN-SUFFIX,syilm.net,REJECT
DOMAIN-SUFFIX,sykty.com,REJECT
DOMAIN-SUFFIX,synacast.com,REJECT
DOMAIN-SUFFIX,syndication.jsadapi.com,REJECT
DOMAIN-SUFFIX,syndication1.viraladnetwork.net,REJECT
DOMAIN-SUFFIX,syofew6o.net,REJECT
DOMAIN-SUFFIX,sysdig.com,REJECT
DOMAIN-SUFFIX,sytcyf.com,REJECT
DOMAIN-SUFFIX,sytsr.com,REJECT
DOMAIN-SUFFIX,sytz1288.com,REJECT
DOMAIN-SUFFIX,szdzbx.com,REJECT
DOMAIN-SUFFIX,szfaq.com,REJECT
DOMAIN-SUFFIX,szggdw.com,REJECT
DOMAIN-SUFFIX,szrk3.com,REJECT
DOMAIN-SUFFIX,szxpsg.com,REJECT
DOMAIN-SUFFIX,t1.visualrevenue.com,REJECT
DOMAIN-SUFFIX,t70123.com,REJECT
DOMAIN-SUFFIX,ta80.com,REJECT
DOMAIN-SUFFIX,taat00889.com,REJECT
DOMAIN-SUFFIX,tad.suning.com,REJECT
DOMAIN-SUFFIX,tag.yoc-adserver.com,REJECT
DOMAIN-SUFFIX,tags2.adshell.net,REJECT
DOMAIN-SUFFIX,tajxgs.com,REJECT
DOMAIN-SUFFIX,talkingdata.net,REJECT
DOMAIN-SUFFIX,tangoutianxia.com,REJECT
DOMAIN-SUFFIX,tansuotv.com,REJECT
DOMAIN-SUFFIX,tanwanyx.com,REJECT
DOMAIN-SUFFIX,tanx.com,REJECT
DOMAIN-SUFFIX,tanzanite.infomine.com,REJECT
DOMAIN-SUFFIX,taobaly.cn,REJECT
DOMAIN-SUFFIX,taobao.qq.com,REJECT
DOMAIN-SUFFIX,taobaoaliyun.cn,REJECT
DOMAIN-SUFFIX,taobayun.cn,REJECT
DOMAIN-SUFFIX,taohanpai.com,REJECT
DOMAIN-SUFFIX,taomato.com,REJECT
DOMAIN-SUFFIX,tapjoy.cn,REJECT
DOMAIN-SUFFIX,tapjoyads.com,REJECT
DOMAIN-SUFFIX,targetedinfo.com,REJECT
DOMAIN-SUFFIX,targetedtopic.com,REJECT
DOMAIN-SUFFIX,tbaocdn.com,REJECT
DOMAIN-SUFFIX,tbjfw.com,REJECT
DOMAIN-SUFFIX,tc600.com,REJECT
DOMAIN-SUFFIX,tcjy66.cc,REJECT
DOMAIN-SUFFIX,tdayi.com,REJECT
DOMAIN-SUFFIX,temp.163.com,REJECT
DOMAIN-SUFFIX,tencentmind.com,REJECT
DOMAIN-SUFFIX,tg.1155t.cn,REJECT
DOMAIN-SUFFIX,tg.52digua.com,REJECT
DOMAIN-SUFFIX,th21333.com,REJECT
DOMAIN-SUFFIX,th7.cn,REJECT
DOMAIN-SUFFIX,thejesperbay.com,REJECT
DOMAIN-SUFFIX,themis.yahoo.com,REJECT
DOMAIN-SUFFIX,thescenseproject.com,REJECT
DOMAIN-SUFFIX,thetestpage.39.net,REJECT
DOMAIN-SUFFIX,thoughtleadr.com,REJECT
DOMAIN-SUFFIX,thxnr.com,REJECT
DOMAIN-SUFFIX,thyvjboy.com,REJECT
DOMAIN-SUFFIX,ti.tradetracker.net,REJECT
DOMAIN-SUFFIX,tiangu99.com,REJECT
DOMAIN-SUFFIX,tianqi777.com,REJECT
DOMAIN-SUFFIX,tianyanzs.com,REJECT
DOMAIN-SUFFIX,ticcdn.com,REJECT
DOMAIN-SUFFIX,tiqcdn.com,REJECT
DOMAIN-SUFFIX,tj.ali213.net,REJECT
DOMAIN-SUFFIX,tj.video.qq.com,REJECT
DOMAIN-SUFFIX,tjqonline.cn,REJECT
DOMAIN-SUFFIX,tk.504pk.com,REJECT
DOMAIN-SUFFIX,tkd777.cn,REJECT
DOMAIN-SUFFIX,tmcs.net,REJECT
DOMAIN-SUFFIX,tongqing2015.com,REJECT
DOMAIN-SUFFIX,toourbb.com,REJECT
DOMAIN-SUFFIX,top267.com,REJECT
DOMAIN-SUFFIX,touclick.com,REJECT
DOMAIN-SUFFIX,tp.sgcn.com,REJECT
DOMAIN-SUFFIX,tpe163.com,REJECT
DOMAIN-SUFFIX,track.bcvcmedia.com,REJECT
DOMAIN-SUFFIX,tracker.yhd.com,REJECT
DOMAIN-SUFFIX,tracking.hostgator.com,REJECT
DOMAIN-SUFFIX,tradeccl.com,REJECT
DOMAIN-SUFFIX,trafficjam.cn,REJECT
DOMAIN-SUFFIX,trafficmp.com,REJECT
DOMAIN-SUFFIX,trzina.com,REJECT
DOMAIN-SUFFIX,tsdlp.com,REJECT
DOMAIN-SUFFIX,tsrc8.com,REJECT
DOMAIN-SUFFIX,tt.biquge.la,REJECT
DOMAIN-SUFFIX,ttlm.cc,REJECT
DOMAIN-SUFFIX,ttlowe.com,REJECT
DOMAIN-SUFFIX,tuadong.com,REJECT
DOMAIN-SUFFIX,tui1999.com,REJECT
DOMAIN-SUFFIX,tui98.cn,REJECT
DOMAIN-SUFFIX,tuigoo.com,REJECT
DOMAIN-SUFFIX,tuiguang.178.com,REJECT
DOMAIN-SUFFIX,tukeai.com,REJECT
DOMAIN-SUFFIX,tukj.net,REJECT
DOMAIN-SUFFIX,twb98.com,REJECT
DOMAIN-SUFFIX,twcczhu.com,REJECT
DOMAIN-SUFFIX,twinplan.com,REJECT
DOMAIN-SUFFIX,twitterzs.com,REJECT
DOMAIN-SUFFIX,twldmx.com,REJECT
DOMAIN-SUFFIX,twzui6.com,REJECT
DOMAIN-SUFFIX,tylll.com,REJECT
DOMAIN-SUFFIX,tzbtw.com,REJECT
DOMAIN-SUFFIX,u.63kc.com,REJECT
DOMAIN-SUFFIX,u.cnzol.com,REJECT
DOMAIN-SUFFIX,u.huoying666.com,REJECT
DOMAIN-SUFFIX,u1.shuaiku.com,REJECT
DOMAIN-SUFFIX,ua.badongo.com,REJECT
DOMAIN-SUFFIX,ubmcmm.baidustatic.com,REJECT
DOMAIN-SUFFIX,uc2.atobo.com.cn,REJECT
DOMAIN-SUFFIX,uc610.com,REJECT
DOMAIN-SUFFIX,ucaliyun.cn,REJECT
DOMAIN-SUFFIX,ucrzgcs.cn,REJECT
DOMAIN-SUFFIX,ucxxii.com,REJECT
DOMAIN-SUFFIX,udrwyjpwjfeg.com,REJECT
DOMAIN-SUFFIX,ueadlian.com,REJECT
DOMAIN-SUFFIX,ugg66.com,REJECT
DOMAIN-SUFFIX,ugvip.com,REJECT
DOMAIN-SUFFIX,ui37.net,REJECT
DOMAIN-SUFFIX,uimserv.net,REJECT
DOMAIN-SUFFIX,ujian.cc,REJECT
DOMAIN-SUFFIX,ujikdd041o.cn,REJECT
DOMAIN-SUFFIX,ukeiae.com,REJECT
DOMAIN-SUFFIX,umeng.co,REJECT
DOMAIN-SUFFIX,umeng.com,REJECT
DOMAIN-SUFFIX,umeng.net,REJECT
DOMAIN-SUFFIX,umtrack.com,REJECT
DOMAIN-SUFFIX,umyai.com,REJECT
DOMAIN-SUFFIX,un1.takefoto.cn,REJECT
DOMAIN-SUFFIX,undm.qibulo.com,REJECT
DOMAIN-SUFFIX,unicast.ign.com,REJECT
DOMAIN-SUFFIX,unicast.msn.com,REJECT
DOMAIN-SUFFIX,unimhk.com,REJECT
DOMAIN-SUFFIX,union.china.com.cn,REJECT
DOMAIN-SUFFIX,uniondm.cz88.net,REJECT
DOMAIN-SUFFIX,unionsy.com,REJECT
DOMAIN-SUFFIX,union-wifi.com,REJECT
DOMAIN-SUFFIX,unlitui.com,REJECT
DOMAIN-SUFFIX,untitled.dwstatic.com,REJECT
DOMAIN-SUFFIX,uoyrsd.com,REJECT
DOMAIN-SUFFIX,up.hiao.com,REJECT
DOMAIN-SUFFIX,up.qingdaonews.com,REJECT
DOMAIN-SUFFIX,urhu.cn,REJECT
DOMAIN-SUFFIX,uri6.com,REJECT
DOMAIN-SUFFIX,ushaqi.com,REJECT
DOMAIN-SUFFIX,usingde.com,REJECT
DOMAIN-SUFFIX,utility.rogersmedia.com,REJECT
DOMAIN-SUFFIX,uvclick.com,REJECT
DOMAIN-SUFFIX,uw9377.com,REJECT
DOMAIN-SUFFIX,uyunad.com,REJECT
DOMAIN-SUFFIX,uzpmrbek.com,REJECT
DOMAIN-SUFFIX,v707070.com,REJECT
DOMAIN-SUFFIX,vad1.jianshen8.com,REJECT
DOMAIN-SUFFIX,vamaker.com,REJECT
DOMAIN-SUFFIX,vas.funshion.com,REJECT
DOMAIN-SUFFIX,vdazz.net,REJECT
DOMAIN-SUFFIX,vedeh.com,REJECT
DOMAIN-SUFFIX,vegent.cn,REJECT
DOMAIN-SUFFIX,vendor1.fitschigogerl.com,REJECT
DOMAIN-SUFFIX,verdict.abc.go.com,REJECT
DOMAIN-SUFFIX,vers80.com,REJECT
DOMAIN-SUFFIX,vi1.ku6img.net,REJECT
DOMAIN-SUFFIX,vi1.souid.com,REJECT
DOMAIN-SUFFIX,vichc.com,REJECT
DOMAIN-SUFFIX,victorjx.com,REJECT
DOMAIN-SUFFIX,video.plista.com,REJECT
DOMAIN-SUFFIX,videondun.com,REJECT
DOMAIN-SUFFIX,viglink.com,REJECT
DOMAIN-SUFFIX,vipads.cn,REJECT
DOMAIN-SUFFIX,voiceads.cn,REJECT
DOMAIN-SUFFIX,voiceads.com,REJECT
DOMAIN-SUFFIX,vpon.com,REJECT
DOMAIN-SUFFIX,vsnoon.com,REJECT
DOMAIN-SUFFIX,vtale.org,REJECT
DOMAIN-SUFFIX,vungle.cn,REJECT
DOMAIN-SUFFIX,vupload.duowan.com,REJECT
DOMAIN-SUFFIX,vvv.ieduw.com,REJECT
DOMAIN-SUFFIX,vvvulqn7.com,REJECT
DOMAIN-SUFFIX,vwws6.net,REJECT
DOMAIN-SUFFIX,w.homes.yahoo.net,REJECT
DOMAIN-SUFFIX,w.xiaopiaoyou.com,REJECT
DOMAIN-SUFFIX,w1.diaoyou.com,REJECT
DOMAIN-SUFFIX,w3989.com,REJECT
DOMAIN-SUFFIX,w5sac788c1.360doc.cn,REJECT
DOMAIN-SUFFIX,w65p.com,REJECT
DOMAIN-SUFFIX,w8.com.cn,REJECT
DOMAIN-SUFFIX,wangdaizao.com,REJECT
DOMAIN-SUFFIX,wangdq.com,REJECT
DOMAIN-SUFFIX,wangsufast.com,REJECT
DOMAIN-SUFFIX,wantaico.com,REJECT
DOMAIN-SUFFIX,wantfour.com,REJECT
DOMAIN-SUFFIX,wap001.bytravel.cn,REJECT
DOMAIN-SUFFIX,wapadv.com,REJECT
DOMAIN-SUFFIX,waps.cn,REJECT
DOMAIN-SUFFIX,wapx.cn,REJECT
DOMAIN-SUFFIX,watson.microsoft.com,REJECT
DOMAIN-SUFFIX,wazero.online,REJECT
DOMAIN-SUFFIX,wda.ydt.com.cn,REJECT
DOMAIN-SUFFIX,wdzsb.com.cn,REJECT
DOMAIN-SUFFIX,weareqy.com,REJECT
DOMAIN-SUFFIX,web.900.la,REJECT
DOMAIN-SUFFIX,webmaster.extabit.com,REJECT
DOMAIN-SUFFIX,webp2p.letv.com,REJECT
DOMAIN-SUFFIX,webterren.com,REJECT
DOMAIN-SUFFIX,weibomingzi.com,REJECT
DOMAIN-SUFFIX,weiqiqu.cn,REJECT
DOMAIN-SUFFIX,weixiangzu.cn,REJECT
DOMAIN-SUFFIX,werpig.com,REJECT
DOMAIN-SUFFIX,wgnlz.com,REJECT
DOMAIN-SUFFIX,wgnmp.com,REJECT
DOMAIN-SUFFIX,whafwl.com,REJECT
DOMAIN-SUFFIX,whistleout.s3.amazonaws.com,REJECT
DOMAIN-SUFFIX,whpxy.com,REJECT
DOMAIN-SUFFIX,whytoss.com,REJECT
DOMAIN-SUFFIX,widget.crowdignite.com,REJECT
DOMAIN-SUFFIX,widget.directory.dailycommercial.com,REJECT
DOMAIN-SUFFIX,widget.kelkoo.com,REJECT
DOMAIN-SUFFIX,widget.raaze.com,REJECT
DOMAIN-SUFFIX,widget.searchschoolsnetwork.com,REJECT
DOMAIN-SUFFIX,widget.shopstyle.com.au,REJECT
DOMAIN-SUFFIX,widget.solarquotes.com.au,REJECT
DOMAIN-SUFFIX,widgets.comcontent.net,REJECT
DOMAIN-SUFFIX,widgets.realestate.com.au,REJECT
DOMAIN-SUFFIX,wikigifth.com,REJECT
DOMAIN-SUFFIX,winads.cn,REJECT
DOMAIN-SUFFIX,winasdaq.com,REJECT
DOMAIN-SUFFIX,winvestern.com.cn,REJECT
DOMAIN-SUFFIX,wiyun.com,REJECT
DOMAIN-SUFFIX,wjguc.com,REJECT
DOMAIN-SUFFIX,wka8.com,REJECT
DOMAIN-SUFFIX,wlkpa.cn,REJECT
DOMAIN-SUFFIX,wlpinnaclesports.eacdn.com,REJECT
DOMAIN-SUFFIX,wo685.com,REJECT
DOMAIN-SUFFIX,wodhid.com,REJECT
DOMAIN-SUFFIX,wole.us,REJECT
DOMAIN-SUFFIX,womenwan.com,REJECT
DOMAIN-SUFFIX,wooboo.com.cn,REJECT
DOMAIN-SUFFIX,wowips.com,REJECT
DOMAIN-SUFFIX,wpwdf.com,REJECT
DOMAIN-SUFFIX,wqmobile.com,REJECT
DOMAIN-SUFFIX,wqsph.net,REJECT
DOMAIN-SUFFIX,wrating.com,REJECT
DOMAIN-SUFFIX,ws341.com,REJECT
DOMAIN-SUFFIX,ws7j.com,REJECT
DOMAIN-SUFFIX,wstztt.com,REJECT
DOMAIN-SUFFIX,wtpn.twenga.co.uk,REJECT
DOMAIN-SUFFIX,wtpn.twenga.de,REJECT
DOMAIN-SUFFIX,wu65.com,REJECT
DOMAIN-SUFFIX,wudang05.com,REJECT
DOMAIN-SUFFIX,wuliao.juqingba.cn,REJECT
DOMAIN-SUFFIX,wumii.cn,REJECT
DOMAIN-SUFFIX,wuwho.cn,REJECT
DOMAIN-SUFFIX,www.32414.com,REJECT
DOMAIN-SUFFIX,www.cliushow.com,REJECT
DOMAIN-SUFFIX,www.hhlian.com,REJECT
DOMAIN-SUFFIX,www.hi686.com,REJECT
DOMAIN-SUFFIX,www.panoramio.com,REJECT
DOMAIN-SUFFIX,www.you1ad.com,REJECT
DOMAIN-SUFFIX,wyhzzy.com,REJECT
DOMAIN-SUFFIX,wyttech.cn,REJECT
DOMAIN-SUFFIX,wzjijia.com,REJECT
DOMAIN-SUFFIX,x.castanet.net,REJECT
DOMAIN-SUFFIX,x.eroticity.net,REJECT
DOMAIN-SUFFIX,x.jd.com,REJECT
DOMAIN-SUFFIX,x.ligatus.com,REJECT
DOMAIN-SUFFIX,x.vipergirls.to,REJECT
DOMAIN-SUFFIX,x9377a.com,REJECT
DOMAIN-SUFFIX,xa9t.com,REJECT
DOMAIN-SUFFIX,xabaitai.com,REJECT
DOMAIN-SUFFIX,xabmjr.com,REJECT
DOMAIN-SUFFIX,xacqp.com,REJECT
DOMAIN-SUFFIX,xavatar.imedao.com,REJECT
DOMAIN-SUFFIX,xbtw.com,REJECT
DOMAIN-SUFFIX,xc.macd.cn,REJECT
DOMAIN-SUFFIX,xcclzs.com,REJECT
DOMAIN-SUFFIX,xcdf.cn,REJECT
DOMAIN-SUFFIX,xchgx.com,REJECT
DOMAIN-SUFFIX,xcjy876.com,REJECT
DOMAIN-SUFFIX,xcy8.com,REJECT
DOMAIN-SUFFIX,xcyjzs.net,REJECT
DOMAIN-SUFFIX,xcyrc.com,REJECT
DOMAIN-SUFFIX,xdbwc.com,REJECT
DOMAIN-SUFFIX,xdcqcyp.com,REJECT
DOMAIN-SUFFIX,xdrig.com,REJECT
DOMAIN-SUFFIX,xdyjt.com,REJECT
DOMAIN-SUFFIX,xe2c.com,REJECT
DOMAIN-SUFFIX,xhbqczl.com,REJECT
DOMAIN-SUFFIX,xhmrv.com,REJECT
DOMAIN-SUFFIX,xhsxgmt.cn,REJECT
DOMAIN-SUFFIX,xhxnkyy.com,REJECT
DOMAIN-SUFFIX,xhydrs.cn,REJECT
DOMAIN-SUFFIX,xiacaidd.com,REJECT
DOMAIN-SUFFIX,xiaobiaoucai.cn,REJECT
DOMAIN-SUFFIX,xiaohei.com,REJECT
DOMAIN-SUFFIX,xiaoyang.mobi,REJECT
DOMAIN-SUFFIX,xiaoyuanzuqiu.cn,REJECT
DOMAIN-SUFFIX,xiaoyutiao.com,REJECT
DOMAIN-SUFFIX,xiaozhen.com,REJECT
DOMAIN-SUFFIX,xiaozhishi852.com,REJECT
DOMAIN-SUFFIX,xiaxuanfu.com,REJECT
DOMAIN-SUFFIX,xibao100.com,REJECT
DOMAIN-SUFFIX,xibei70.com,REJECT
DOMAIN-SUFFIX,xifatime.com,REJECT
DOMAIN-SUFFIX,xihashuale.com,REJECT
DOMAIN-SUFFIX,xilele.com,REJECT
DOMAIN-SUFFIX,xiliweisha.cn,REJECT
DOMAIN-SUFFIX,xinasiaj.com,REJECT
DOMAIN-SUFFIX,xingjuhe.com,REJECT
DOMAIN-SUFFIX,xinju.cc,REJECT
DOMAIN-SUFFIX,xinray.com,REJECT
DOMAIN-SUFFIX,xjidian.com,REJECT
DOMAIN-SUFFIX,xk2012.com,REJECT
DOMAIN-SUFFIX,xkwfao.com,REJECT
DOMAIN-SUFFIX,xlwnx.com,REJECT
DOMAIN-SUFFIX,xm9178.com,REJECT
DOMAIN-SUFFIX,xmcmn.com,REJECT
DOMAIN-SUFFIX,xmcxz.com,REJECT
DOMAIN-SUFFIX,xmrts.com,REJECT
DOMAIN-SUFFIX,xmshqh.com,REJECT
DOMAIN-SUFFIX,xmsqz.com,REJECT
DOMAIN-SUFFIX,xnjpg.com,REJECT
DOMAIN-SUFFIX,xoredi.com,REJECT
DOMAIN-SUFFIX,xp3366.com,REJECT
DOMAIN-SUFFIX,xpqfc.com,REJECT
DOMAIN-SUFFIX,xq12.com,REJECT
DOMAIN-SUFFIX,xq199.com,REJECT
DOMAIN-SUFFIX,xstar.cc,REJECT
DOMAIN-SUFFIX,xtgreat.com,REJECT
DOMAIN-SUFFIX,xtxa.net,REJECT
DOMAIN-SUFFIX,xuanmeiguoji.com,REJECT
DOMAIN-SUFFIX,xue.zbyw.cn,REJECT
DOMAIN-SUFFIX,xul478.com,REJECT
DOMAIN-SUFFIX,xulizui6.com,REJECT
DOMAIN-SUFFIX,xxad.cc,REJECT
DOMAIN-SUFFIX,xxhrd.com,REJECT
DOMAIN-SUFFIX,xxwkjl.com,REJECT
DOMAIN-SUFFIX,xxyzwtsylw.com,REJECT
DOMAIN-SUFFIX,xy.com,REJECT
DOMAIN-SUFFIX,xycnz.com,REJECT
DOMAIN-SUFFIX,xyimg.net,REJECT
DOMAIN-SUFFIX,xyly2016.com,REJECT
DOMAIN-SUFFIX,xyqptm.com,REJECT
DOMAIN-SUFFIX,xyqxr.com,REJECT
DOMAIN-SUFFIX,xyrhd.com,REJECT
DOMAIN-SUFFIX,xyssp.com,REJECT
DOMAIN-SUFFIX,xytom.com,REJECT
DOMAIN-SUFFIX,xyxy01.com,REJECT
DOMAIN-SUFFIX,xztms.com,REJECT
DOMAIN-SUFFIX,xzyituo.com,REJECT
DOMAIN-SUFFIX,xzzyi.com,REJECT
DOMAIN-SUFFIX,y.damifan.cn,REJECT
DOMAIN-SUFFIX,yadro.ru,REJECT
DOMAIN-SUFFIX,yageben.com,REJECT
DOMAIN-SUFFIX,yandui.com,REJECT
DOMAIN-SUFFIX,yangdasen.cn,REJECT
DOMAIN-SUFFIX,yanglaopt.net,REJECT
DOMAIN-SUFFIX,yaohq.com,REJECT
DOMAIN-SUFFIX,yatemy.cn,REJECT
DOMAIN-SUFFIX,yb.torchbrowser.com,REJECT
DOMAIN-SUFFIX,yccdn.com,REJECT
DOMAIN-SUFFIX,ychml.com,REJECT
DOMAIN-SUFFIX,ychun03.com,REJECT
DOMAIN-SUFFIX,ydlnt.com,REJECT
DOMAIN-SUFFIX,yea.uploadimagex.com,REJECT
DOMAIN-SUFFIX,yeabble.com,REJECT
DOMAIN-SUFFIX,yeas.yahoo.co.jp,REJECT
DOMAIN-SUFFIX,yengo.com,REJECT
DOMAIN-SUFFIX,yesbeby.whies.info,REJECT
DOMAIN-SUFFIX,yezilm.com,REJECT
DOMAIN-SUFFIX,yf898.com,REJECT
DOMAIN-SUFFIX,yfycy.com,REJECT
DOMAIN-SUFFIX,yhtcd.com,REJECT
DOMAIN-SUFFIX,yhzm.cc,REJECT
DOMAIN-SUFFIX,yidulive.net,REJECT
DOMAIN-SUFFIX,yieldmanager.com,REJECT
DOMAIN-SUFFIX,yigao.com,REJECT
DOMAIN-SUFFIX,yigyx.com,REJECT
DOMAIN-SUFFIX,yiiwoo.com,REJECT
DOMAIN-SUFFIX,yijia2009.com,REJECT
DOMAIN-SUFFIX,yijifen.com,REJECT
DOMAIN-SUFFIX,yin1.zgpingshu.com,REJECT
DOMAIN-SUFFIX,yinhaijuan.com,REJECT
DOMAIN-SUFFIX,yinooo.com,REJECT
DOMAIN-SUFFIX,yinyuehu.cn,REJECT
DOMAIN-SUFFIX,yiqifa.com,REJECT
DOMAIN-SUFFIX,yiranxian.cn,REJECT
DOMAIN-SUFFIX,yiwk.com,REJECT
DOMAIN-SUFFIX,yiwuds.com,REJECT
DOMAIN-SUFFIX,yixui.com,REJECT
DOMAIN-SUFFIX,yk0712.com,REJECT
DOMAIN-SUFFIX,ykbei.com,REJECT
DOMAIN-SUFFIX,ykjmy.com,REJECT
DOMAIN-SUFFIX,yktj.yzz.cn,REJECT
DOMAIN-SUFFIX,ykxwn.com,REJECT
DOMAIN-SUFFIX,ylunion.com,REJECT
DOMAIN-SUFFIX,ymapp.com,REJECT
DOMAIN-SUFFIX,ymcdn.cn,REJECT
DOMAIN-SUFFIX,ymcqb.com,REJECT
DOMAIN-SUFFIX,yndianju.com,REJECT
DOMAIN-SUFFIX,ynmbz.com,REJECT
DOMAIN-SUFFIX,yongkang6.com,REJECT
DOMAIN-SUFFIX,yongv.com,REJECT
DOMAIN-SUFFIX,yooli.com,REJECT
DOMAIN-SUFFIX,youfumei.com,REJECT
DOMAIN-SUFFIX,youmi.net,REJECT
DOMAIN-SUFFIX,yousee.com,REJECT
DOMAIN-SUFFIX,youxiaoad.com,REJECT
DOMAIN-SUFFIX,youxicool.net,REJECT
DOMAIN-SUFFIX,yoyi.com.cn,REJECT
DOMAIN-SUFFIX,ypmeiwen.com,REJECT
DOMAIN-SUFFIX,ypmob.com,REJECT
DOMAIN-SUFFIX,yqw88.com,REJECT
DOMAIN-SUFFIX,yrt7dgkf.exashare.com,REJECT
DOMAIN-SUFFIX,yrxmr.com,REJECT
DOMAIN-SUFFIX,ysjwj.com,REJECT
DOMAIN-SUFFIX,ysm.yahoo.com,REJECT
DOMAIN-SUFFIX,yug8.com,REJECT
DOMAIN-SUFFIX,yule8.net,REJECT
DOMAIN-SUFFIX,yulzs.com,REJECT
DOMAIN-SUFFIX,yun1.yahoo001.com,REJECT
DOMAIN-SUFFIX,yunanfuwuqi.com,REJECT
DOMAIN-SUFFIX,yunbofangbt.com,REJECT
DOMAIN-SUFFIX,yunjiasu.com,REJECT
DOMAIN-SUFFIX,yunshipei.com,REJECT
DOMAIN-SUFFIX,ywjxsp168.cn,REJECT
DOMAIN-SUFFIX,yxhxs.com,REJECT
DOMAIN-SUFFIX,yxjad.com,REJECT
DOMAIN-SUFFIX,yxszy.com,REJECT
DOMAIN-SUFFIX,yxxwyz.com,REJECT
DOMAIN-SUFFIX,yy58ju.com,REJECT
DOMAIN-SUFFIX,yyp17.com,REJECT
DOMAIN-SUFFIX,yzh360.com,REJECT
DOMAIN-SUFFIX,yzygo.com,REJECT
DOMAIN-SUFFIX,yzytb.com,REJECT
DOMAIN-SUFFIX,z.nowscore.com,REJECT
DOMAIN-SUFFIX,zads.care2.com,REJECT
DOMAIN-SUFFIX,zamar.cn,REJECT
DOMAIN-SUFFIX,zampdsp.com,REJECT
DOMAIN-SUFFIX,zapads.zapak.com,REJECT
DOMAIN-SUFFIX,zb.nxing.cn,REJECT
DOMAIN-SUFFIX,zcrtd.com,REJECT
DOMAIN-SUFFIX,zdjby.cn,REJECT
DOMAIN-SUFFIX,ze5.com,REJECT
DOMAIN-SUFFIX,zedo.com,REJECT
DOMAIN-SUFFIX,zeus.qj.net,REJECT
DOMAIN-SUFFIX,zgc66.com,REJECT
DOMAIN-SUFFIX,zgfszs.com,REJECT
DOMAIN-SUFFIX,zgksb.com,REJECT
DOMAIN-SUFFIX,zgunion.cn,REJECT
DOMAIN-SUFFIX,zgyemy.com,REJECT
DOMAIN-SUFFIX,zhanzhang.net,REJECT
DOMAIN-SUFFIX,zhao258.com,REJECT
DOMAIN-SUFFIX,zhaoshang8.com,REJECT
DOMAIN-SUFFIX,zhichi08.com,REJECT
DOMAIN-SUFFIX,zhidian3g.cn,REJECT
DOMAIN-SUFFIX,zhifenjie.com,REJECT
DOMAIN-SUFFIX,zhihei.com,REJECT
DOMAIN-SUFFIX,zhihu.xmcimg.com,REJECT
DOMAIN-SUFFIX,zhiong.net,REJECT
DOMAIN-SUFFIX,zhiyuanteam.com,REJECT
DOMAIN-SUFFIX,zhiziyun.com,REJECT
DOMAIN-SUFFIX,zhongchouyan.com,REJECT
DOMAIN-SUFFIX,zhuba8.com,REJECT
DOMAIN-SUFFIX,zhudiaosz.com,REJECT
DOMAIN-SUFFIX,zhybzp.cn,REJECT
DOMAIN-SUFFIX,zisunion.com,REJECT
DOMAIN-SUFFIX,zizcy.com,REJECT
DOMAIN-SUFFIX,zjbdt.com,REJECT
DOMAIN-SUFFIX,zjhim.com,REJECT
DOMAIN-SUFFIX,zkrdy.com,REJECT
DOMAIN-SUFFIX,zo66.com,REJECT
DOMAIN-SUFFIX,zp22938576.com,REJECT
DOMAIN-SUFFIX,zq84.com,REJECT
DOMAIN-SUFFIX,zqworks.com,REJECT
DOMAIN-SUFFIX,zqzxz.com,REJECT
DOMAIN-SUFFIX,zrpfk.com,REJECT
DOMAIN-SUFFIX,zsdexun.com.cn,REJECT
DOMAIN-SUFFIX,zsxpx.com,REJECT
DOMAIN-SUFFIX,zt2088.com,REJECT
DOMAIN-SUFFIX,ztidu.com,REJECT
DOMAIN-SUFFIX,ztxbd.com,REJECT
DOMAIN-SUFFIX,zuche321.com,REJECT
DOMAIN-SUFFIX,zuiceshi.net,REJECT
DOMAIN-SUFFIX,zws.avvo.com,REJECT
DOMAIN-SUFFIX,zxjjzx.com,REJECT
DOMAIN-SUFFIX,zybpj.com,REJECT
DOMAIN-SUFFIX,zymro.com,REJECT
DOMAIN-SUFFIX,zytwq.net,REJECT
DOMAIN-SUFFIX,zzbaowen.com,REJECT
DOMAIN-SUFFIX,zzimg.51.la,REJECT
DOMAIN-SUFFIX,zzrcz.com,REJECT
DOMAIN-SUFFIX,zzsx8.com,REJECT

IP-CIDR,1.3.0.10/32,REJECT,no-resolve
IP-CIDR,101.227.119.0/24,REJECT,no-resolve
IP-CIDR,101.227.12.0/23,REJECT,no-resolve
IP-CIDR,101.227.14.0/24,REJECT,no-resolve
IP-CIDR,101.227.200.0/24,REJECT,no-resolve
IP-CIDR,103.249.254.113/32,REJECT,no-resolve
IP-CIDR,104.195.62.12/32,REJECT,no-resolve
IP-CIDR,104.197.140.120/32,REJECT,no-resolve
IP-CIDR,104.198.198.188/32,REJECT,no-resolve
IP-CIDR,106.187.95.251/32,REJECT,no-resolve
IP-CIDR,107.21.113.76/32,REJECT,no-resolve
IP-CIDR,108.171.248.234/32,REJECT,no-resolve
IP-CIDR,111.175.220.160/29,REJECT,no-resolve
IP-CIDR,111.206.13.250/31,REJECT,no-resolve
IP-CIDR,111.206.13.60/30,REJECT,no-resolve
IP-CIDR,111.206.13.64/28,REJECT,no-resolve
IP-CIDR,111.206.13.80/32,REJECT,no-resolve
IP-CIDR,111.206.22.0/24,REJECT,no-resolve
IP-CIDR,111.30.135.167/32,REJECT,no-resolve
IP-CIDR,111.63.135.0/24,REJECT,no-resolve
IP-CIDR,111.73.45.147/32,REJECT,no-resolve
IP-CIDR,112.124.115.215/32,REJECT,no-resolve
IP-CIDR,112.74.95.46/32,REJECT,no-resolve
IP-CIDR,113.207.57.24/32,REJECT,no-resolve
IP-CIDR,113.57.230.88/32,REJECT,no-resolve
IP-CIDR,114.247.28.96/32,REJECT,no-resolve
IP-CIDR,114.95.102.77/32,REJECT,no-resolve
IP-CIDR,115.29.141.121/32,REJECT,no-resolve
IP-CIDR,115.29.247.48/32,REJECT,no-resolve
IP-CIDR,116.206.22.7/32,REJECT,no-resolve
IP-CIDR,116.55.227.242/32,REJECT,no-resolve
IP-CIDR,117.25.133.209/32,REJECT,no-resolve
IP-CIDR,118.144.88.215/32,REJECT,no-resolve
IP-CIDR,118.144.88.216/32,REJECT,no-resolve
IP-CIDR,119.188.13.0/24,REJECT,no-resolve
IP-CIDR,119.4.249.166/32,REJECT,no-resolve
IP-CIDR,120.132.63.203/32,REJECT,no-resolve
IP-CIDR,120.26.151.246/32,REJECT,no-resolve
IP-CIDR,120.27.34.156/32,REJECT,no-resolve
IP-CIDR,120.55.199.139/32,REJECT,no-resolve
IP-CIDR,120.80.57.123/32,REJECT,no-resolve
IP-CIDR,121.201.108.2/32,REJECT,no-resolve
IP-CIDR,121.201.11.95/32,REJECT,no-resolve
IP-CIDR,121.251.255.0/24,REJECT,no-resolve
IP-CIDR,121.43.75.169/32,REJECT,no-resolve
IP-CIDR,122.226.223.163/32,REJECT,no-resolve
IP-CIDR,122.227.254.195/32,REJECT,no-resolve
IP-CIDR,122.228.236.165/32,REJECT,no-resolve
IP-CIDR,123.125.111.0/24,REJECT,no-resolve
IP-CIDR,123.139.154.201/24,REJECT,no-resolve
IP-CIDR,123.57.94.184/32,REJECT,no-resolve
IP-CIDR,123.59.152.170/32,REJECT,no-resolve
IP-CIDR,124.160.194.11/32,REJECT,no-resolve
IP-CIDR,124.232.160.178/32,REJECT,no-resolve
IP-CIDR,125.46.61.28/32,REJECT,no-resolve
IP-CIDR,139.159.32.82/32,REJECT,no-resolve
IP-CIDR,146.148.85.61/32,REJECT,no-resolve
IP-CIDR,162.212.181.32/32,REJECT,no-resolve
IP-CIDR,180.166.52.24/32,REJECT,no-resolve
IP-CIDR,180.76.162.60/32,REJECT,no-resolve
IP-CIDR,180.76.171.28/32,REJECT,no-resolve
IP-CIDR,182.92.81.104/32,REJECT,no-resolve
IP-CIDR,183.131.79.130/32,REJECT,no-resolve
IP-CIDR,183.59.53.188/32,REJECT,no-resolve
IP-CIDR,198.40.52.11/32,REJECT,no-resolve
IP-CIDR,205.209.138.102/32,REJECT,no-resolve
IP-CIDR,211.103.159.32/32,REJECT,no-resolve
IP-CIDR,211.137.132.89/32,REJECT,no-resolve
IP-CIDR,211.149.225.23/32,REJECT,no-resolve
IP-CIDR,211.167.105.131/32,REJECT,no-resolve
IP-CIDR,211.98.71.195/32,REJECT,no-resolve
IP-CIDR,211.98.71.196/32,REJECT,no-resolve
IP-CIDR,218.25.246.118/32,REJECT,no-resolve
IP-CIDR,219.234.83.60/32,REJECT,no-resolve
IP-CIDR,220.115.251.25/32,REJECT,no-resolve
IP-CIDR,220.196.52.141/32,REJECT,no-resolve
IP-CIDR,221.179.140.0/24,REJECT,no-resolve
IP-CIDR,221.179.140.145/32,REJECT,no-resolve
IP-CIDR,221.179.183.0/24,REJECT,no-resolve
IP-CIDR,221.179.191.0/24,REJECT,no-resolve
IP-CIDR,221.204.213.222/32,REJECT,no-resolve
IP-CIDR,221.228.17.152/32,REJECT,no-resolve
IP-CIDR,221.228.214.101/32,REJECT,no-resolve
IP-CIDR,23.42.186.24/32,REJECT,no-resolve
IP-CIDR,23.66.147.48/32,REJECT,no-resolve
IP-CIDR,27.255.67.120/32,REJECT,no-resolve
IP-CIDR,42.51.146.207/32,REJECT,no-resolve
IP-CIDR,45.34.240.72/32,REJECT,no-resolve
IP-CIDR,46.165.197.153/32,REJECT,no-resolve
IP-CIDR,46.165.197.231/32,REJECT,no-resolve
IP-CIDR,47.90.50.177/32,REJECT,no-resolve
IP-CIDR,47.94.89.32/32,REJECT,no-resolve
IP-CIDR,58.215.179.159/32,REJECT,no-resolve
IP-CIDR,60.190.139.164/32,REJECT,no-resolve
IP-CIDR,60.191.124.196/32,REJECT,no-resolve
IP-CIDR,60.210.17.12/24,REJECT,no-resolve
IP-CIDR,61.132.216.232/32,REJECT,no-resolve
IP-CIDR,61.132.221.146/32,REJECT,no-resolve
IP-CIDR,61.132.255.212/32,REJECT,no-resolve
IP-CIDR,61.132.255.222/32,REJECT,no-resolve
IP-CIDR,61.132.255.232/32,REJECT,no-resolve
IP-CIDR,61.147.184.18/32,REJECT,no-resolve
IP-CIDR,61.152.223.15/32,REJECT,no-resolve
IP-CIDR,61.160.200.242/16,REJECT,no-resolve
IP-CIDR,61.174.50.167/32,REJECT,no-resolve
IP-CIDR,61.174.50.211/32,REJECT,no-resolve
IP-CIDR,61.191.12.74/32,REJECT,no-resolve
IP-CIDR,61.191.206.4/32,REJECT,no-resolve
IP-CIDR,69.28.57.245/32,REJECT,no-resolve
IP-CIDR,74.117.182.77/32,REJECT,no-resolve
IP-CIDR,78.140.131.214/32,REJECT,no-resolve



// Client
PROCESS-NAME,Backup and Sync,Proxy
PROCESS-NAME,Day One,Proxy
PROCESS-NAME,Dropbox,Proxy,force-remote-dns
PROCESS-NAME,node-webkit,Proxy
PROCESS-NAME,Spotify,Proxy,force-remote-dns
PROCESS-NAME,Telegram,Proxy,force-remote-dns
PROCESS-NAME,Tweetbot,Proxy,force-remote-dns
PROCESS-NAME,Twitter,Proxy,force-remote-dns

// UA
USER-AGENT,*Telegram*,Proxy,force-remote-dns
USER-AGENT,Argo*,Proxy
USER-AGENT,Instagram*,Proxy,force-remote-dns
USER-AGENT,Speedtest*,Proxy
USER-AGENT,WhatsApp*,Proxy,force-remote-dns
USER-AGENT,YouTube*,Proxy,force-remote-dns



# PROXY

// Line
DOMAIN-SUFFIX,lin.ee,Proxy
DOMAIN-SUFFIX,line.me,Proxy
DOMAIN-SUFFIX,line.naver.jp,Proxy,force-remote-dns
DOMAIN-SUFFIX,line-apps.com,Proxy
DOMAIN-SUFFIX,line-cdn.net,Proxy
DOMAIN-SUFFIX,line-scdn.net,Proxy
DOMAIN-SUFFIX,nhncorp.jp,Proxy
IP-CIDR,125.209.222.202/32,Proxy,no-resolve

// MytvSUPER
DOMAIN-KEYWORD,nowtv100,Proxy
DOMAIN-KEYWORD,rthklive,Proxy
DOMAIN-SUFFIX,mytvsuper.com,Proxy
DOMAIN-SUFFIX,tvb.com,Proxy

// Netflix
DOMAIN-SUFFIX,netflix.com,Proxy
DOMAIN-SUFFIX,netflix.net,Proxy
DOMAIN-SUFFIX,nflxext.com,Proxy
DOMAIN-SUFFIX,nflximg.com,Proxy
DOMAIN-SUFFIX,nflximg.net,Proxy
DOMAIN-SUFFIX,nflxvideo.net,Proxy

// Top blocked sites
DOMAIN-SUFFIX,2o7.net,Proxy
DOMAIN-SUFFIX,4everProxy.com,Proxy
DOMAIN-SUFFIX,4shared.com,Proxy
DOMAIN-SUFFIX,4sqi.net,Proxy
DOMAIN-SUFFIX,9to5mac.com,Proxy
DOMAIN-SUFFIX,abpchina.org,Proxy
DOMAIN-SUFFIX,accountkit.com,Proxy
DOMAIN-SUFFIX,adblockplus.org,Proxy
DOMAIN-SUFFIX,adobe.com,Proxy
DOMAIN-SUFFIX,adobedtm.com,Proxy
DOMAIN-SUFFIX,aerisapi.com,Proxy
DOMAIN-SUFFIX,akamaihd.net,Proxy
DOMAIN-SUFFIX,alfredapp.com,Proxy
DOMAIN-SUFFIX,allconnected.co,Proxy
DOMAIN-SUFFIX,amazon.com,Proxy
DOMAIN-SUFFIX,amazonaws.com,Proxy
DOMAIN-SUFFIX,amplitude.com,Proxy
DOMAIN-SUFFIX,ampproject.com,Proxy
DOMAIN-SUFFIX,ampproject.net,Proxy
DOMAIN-SUFFIX,ampproject.org,Proxy
DOMAIN-SUFFIX,ancsconf.org,Proxy
DOMAIN-SUFFIX,android.com,Proxy
DOMAIN-SUFFIX,androidify.com,Proxy
DOMAIN-SUFFIX,android-x86.org,Proxy
DOMAIN-SUFFIX,angularjs.org,Proxy
DOMAIN-SUFFIX,anthonycalzadilla.com,Proxy
DOMAIN-SUFFIX,aol.com,Proxy
DOMAIN-SUFFIX,aolcdn.com,Proxy
DOMAIN-SUFFIX,apigee.com,Proxy
DOMAIN-SUFFIX,apk-dl.com,Proxy
DOMAIN-SUFFIX,apkpure.com,Proxy
DOMAIN-SUFFIX,appdownloader.net,Proxy
DOMAIN-SUFFIX,apple-dns.net,Proxy
DOMAIN-SUFFIX,appshopper.com,Proxy
DOMAIN-SUFFIX,arcgis.com,Proxy
DOMAIN-SUFFIX,archive.is,Proxy
DOMAIN-SUFFIX,archive.org,Proxy
DOMAIN-SUFFIX,archives.gov,Proxy
DOMAIN-SUFFIX,armorgames.com,Proxy
DOMAIN-SUFFIX,aspnetcdn.com,Proxy
DOMAIN-SUFFIX,async.be,Proxy
DOMAIN-SUFFIX,att.com,Proxy
DOMAIN-SUFFIX,awsstatic.com,Proxy
DOMAIN-SUFFIX,azureedge.net,Proxy
DOMAIN-SUFFIX,azurewebsites.net,Proxy
DOMAIN-SUFFIX,bandisoft.com,Proxy
DOMAIN-SUFFIX,bbtoystore.com,Proxy
DOMAIN-SUFFIX,betvictor.com,Proxy
DOMAIN-SUFFIX,bigsound.org,Proxy
DOMAIN-SUFFIX,bintray.com,Proxy
DOMAIN-SUFFIX,bit.com,Proxy
DOMAIN-SUFFIX,bit.do,Proxy
DOMAIN-SUFFIX,bit.ly,Proxy
DOMAIN-SUFFIX,bitbucket.org,Proxy
DOMAIN-SUFFIX,bitcointalk.org,Proxy
DOMAIN-SUFFIX,bitshare.com,Proxy
DOMAIN-SUFFIX,bjango.com,Proxy
DOMAIN-SUFFIX,bkrtx.com,Proxy
DOMAIN-SUFFIX,blizzard.com,Proxy
DOMAIN-SUFFIX,blog.com,Proxy
DOMAIN-SUFFIX,blogcdn.com,Proxy
DOMAIN-SUFFIX,blogger.com,Proxy
DOMAIN-SUFFIX,bloglovin.com,Proxy
DOMAIN-SUFFIX,blogsmithmedia.com,Proxy
DOMAIN-SUFFIX,blogspot.hk,Proxy
DOMAIN-SUFFIX,bloomberg.com,Proxy
DOMAIN-SUFFIX,books.com.tw,Proxy
DOMAIN-SUFFIX,boomtrain.com,Proxy
DOMAIN-SUFFIX,box.com,Proxy
DOMAIN-SUFFIX,box.net,Proxy
DOMAIN-SUFFIX,boxun.com,Proxy
DOMAIN-SUFFIX,cachefly.net,Proxy
DOMAIN-SUFFIX,cbc.ca,Proxy
DOMAIN-SUFFIX,cdn.segment.com,Proxy
DOMAIN-SUFFIX,cdnst.net,Proxy
DOMAIN-SUFFIX,celestrak.com,Proxy
DOMAIN-SUFFIX,census.gov,Proxy
DOMAIN-SUFFIX,certificate-transparency.org,Proxy
DOMAIN-SUFFIX,chinatimes.com,Proxy
DOMAIN-SUFFIX,chrome.com,Proxy
DOMAIN-SUFFIX,chromecast.com,Proxy
DOMAIN-SUFFIX,chromercise.com,Proxy
DOMAIN-SUFFIX,chromestatus.com,Proxy
DOMAIN-SUFFIX,chromium.org,Proxy
DOMAIN-SUFFIX,cl.ly,Proxy
DOMAIN-SUFFIX,cloudflare.com,Proxy
DOMAIN-SUFFIX,cloudfront.net,Proxy
DOMAIN-SUFFIX,cloudmagic.com,Proxy
DOMAIN-SUFFIX,cmail19.com,Proxy
DOMAIN-SUFFIX,cnet.com,Proxy
DOMAIN-SUFFIX,cnn.com,Proxy
DOMAIN-SUFFIX,cocoapods.org,Proxy
DOMAIN-SUFFIX,comodoca.com,Proxy
DOMAIN-SUFFIX,content.office.net,Proxy
DOMAIN-SUFFIX,d.pr,Proxy
DOMAIN-SUFFIX,danilo.to,Proxy
DOMAIN-SUFFIX,daolan.net,Proxy
DOMAIN-SUFFIX,data-vocabulary.org,Proxy
DOMAIN-SUFFIX,dayone.me,Proxy
DOMAIN-SUFFIX,db.tt,Proxy
DOMAIN-SUFFIX,dcmilitary.com,Proxy
DOMAIN-SUFFIX,deja.com,Proxy
DOMAIN-SUFFIX,demdex.net,Proxy
DOMAIN-SUFFIX,deskconnect.com,Proxy
DOMAIN-SUFFIX,digicert.com,Proxy
DOMAIN-SUFFIX,digisfera.com,Proxy
DOMAIN-SUFFIX,digitaltrends.com,Proxy
DOMAIN-SUFFIX,disconnect.me,Proxy
DOMAIN-SUFFIX,disq.us,Proxy
DOMAIN-SUFFIX,disqus.com,Proxy
DOMAIN-SUFFIX,disquscdn.com,Proxy
DOMAIN-SUFFIX,dnsimple.com,Proxy
DOMAIN-SUFFIX,docker.com,Proxy
DOMAIN-SUFFIX,dribbble.com,Proxy
DOMAIN-SUFFIX,droplr.com,Proxy
DOMAIN-SUFFIX,duckduckgo.com,Proxy
DOMAIN-SUFFIX,dueapp.com,Proxy
DOMAIN-SUFFIX,dw.com,Proxy
DOMAIN-SUFFIX,easybib.com,Proxy
DOMAIN-SUFFIX,economist.com,Proxy
DOMAIN-SUFFIX,edgecastcdn.net,Proxy
DOMAIN-SUFFIX,edgekey.net,Proxy
DOMAIN-SUFFIX,edgesuite.net,Proxy
DOMAIN-SUFFIX,engadget.com,Proxy
DOMAIN-SUFFIX,entrust.net,Proxy
DOMAIN-SUFFIX,eurekavpt.com,Proxy
DOMAIN-SUFFIX,evernote.com,Proxy
DOMAIN-SUFFIX,extmatrix.com,Proxy
DOMAIN-SUFFIX,eyny.com,Proxy
DOMAIN-SUFFIX,fabric.io,Proxy
DOMAIN-SUFFIX,fast.com,Proxy
DOMAIN-SUFFIX,fastly.net,Proxy
DOMAIN-SUFFIX,fc2.com,Proxy
DOMAIN-SUFFIX,feedburner.com,Proxy
DOMAIN-SUFFIX,feedly.com,Proxy
DOMAIN-SUFFIX,feedsportal.com,Proxy
DOMAIN-SUFFIX,fiftythree.com,Proxy
DOMAIN-SUFFIX,firebaseio.com,Proxy
DOMAIN-SUFFIX,flexibits.com,Proxy
DOMAIN-SUFFIX,flickr.com,Proxy
DOMAIN-SUFFIX,flipboard.com,Proxy
DOMAIN-SUFFIX,flipkart.com,Proxy
DOMAIN-SUFFIX,flitto.com,Proxy
DOMAIN-SUFFIX,freeopenProxy.com,Proxy
DOMAIN-SUFFIX,fullstory.com,Proxy
DOMAIN-SUFFIX,fzlm.net,Proxy
DOMAIN-SUFFIX,g.co,Proxy
DOMAIN-SUFFIX,gabia.net,Proxy
DOMAIN-SUFFIX,garena.com,Proxy
DOMAIN-SUFFIX,geni.us,Proxy
DOMAIN-SUFFIX,get.how,Proxy
DOMAIN-SUFFIX,getcloudapp.com,Proxy
DOMAIN-SUFFIX,getfoxyProxy.org,Proxy
DOMAIN-SUFFIX,getlantern.org,Proxy
DOMAIN-SUFFIX,getmdl.io,Proxy
DOMAIN-SUFFIX,getpricetag.com,Proxy
DOMAIN-SUFFIX,gfw.press,Proxy
DOMAIN-SUFFIX,gfx.ms,Proxy
DOMAIN-SUFFIX,ggpht.com,Proxy
DOMAIN-SUFFIX,ghostnoteapp.com,Proxy
DOMAIN-SUFFIX,git.io,Proxy
DOMAIN-SUFFIX,github.com,Proxy
DOMAIN-SUFFIX,github.io,Proxy
DOMAIN-SUFFIX,githubapp.com,Proxy
DOMAIN-SUFFIX,githubusercontent.com,Proxy
DOMAIN-SUFFIX,globalsign.com,Proxy
DOMAIN-SUFFIX,gmodules.com,Proxy
DOMAIN-SUFFIX,go.com,Proxy
DOMAIN-SUFFIX,go.jp,Proxy
DOMAIN-SUFFIX,godaddy.com,Proxy
DOMAIN-SUFFIX,golang.org,Proxy
DOMAIN-SUFFIX,gongm.in,Proxy
DOMAIN-SUFFIX,goo.gl,Proxy
DOMAIN-SUFFIX,goodreaders.com,Proxy
DOMAIN-SUFFIX,goodreads.com,Proxy
DOMAIN-SUFFIX,gravatar.com,Proxy
DOMAIN-SUFFIX,gstatic.cn,Proxy
DOMAIN-SUFFIX,gstatic.com,Proxy
DOMAIN-SUFFIX,gunsamerica.com,Proxy
DOMAIN-SUFFIX,gvt0.com,Proxy
DOMAIN-SUFFIX,helpshift.com,Proxy
DOMAIN-SUFFIX,hockeyapp.net,Proxy
DOMAIN-SUFFIX,homedepot.com,Proxy
DOMAIN-SUFFIX,hootsuite.com,Proxy
DOMAIN-SUFFIX,hotmail.com,Proxy
DOMAIN-SUFFIX,howtoforge.com,Proxy
DOMAIN-SUFFIX,iam.soy,Proxy
DOMAIN-SUFFIX,icoco.com,Proxy
DOMAIN-SUFFIX,icons8.com,Proxy
DOMAIN-SUFFIX,ift.tt,Proxy
DOMAIN-SUFFIX,ifttt.com,Proxy
DOMAIN-SUFFIX,imageshack.us,Proxy
DOMAIN-SUFFIX,img.ly,Proxy
DOMAIN-SUFFIX,imgur.com,Proxy
DOMAIN-SUFFIX,imore.com,Proxy
DOMAIN-SUFFIX,ingress.com ,Proxy
DOMAIN-SUFFIX,insder.co,Proxy
DOMAIN-SUFFIX,instapaper.com,Proxy
DOMAIN-SUFFIX,instructables.com,Proxy
DOMAIN-SUFFIX,io.io,Proxy
DOMAIN-SUFFIX,ipn.li,Proxy
DOMAIN-SUFFIX,is.gd,Proxy
DOMAIN-SUFFIX,issuu.com,Proxy
DOMAIN-SUFFIX,itgonglun.com,Proxy
DOMAIN-SUFFIX,itun.es,Proxy
DOMAIN-SUFFIX,ixquick.com,Proxy
DOMAIN-SUFFIX,j.mp,Proxy
DOMAIN-SUFFIX,js.revsci.net,Proxy
DOMAIN-SUFFIX,jshint.com,Proxy
DOMAIN-SUFFIX,jtvnw.net,Proxy
DOMAIN-SUFFIX,justgetflux.com,Proxy
DOMAIN-SUFFIX,kakao.co.kr,Proxy
DOMAIN-SUFFIX,kakao.com,Proxy
DOMAIN-SUFFIX,kakaocdn.net,Proxy
DOMAIN-SUFFIX,kat.cr,Proxy
DOMAIN-SUFFIX,kenengba.com,Proxy
DOMAIN-SUFFIX,klip.me,Proxy
DOMAIN-SUFFIX,leancloud.com,Proxy
DOMAIN-SUFFIX,leetcode.com,Proxy
DOMAIN-SUFFIX,libsyn.com,Proxy
DOMAIN-SUFFIX,licdn.com,Proxy
DOMAIN-SUFFIX,lightboxcdn.com,Proxy
DOMAIN-SUFFIX,like.com,Proxy
DOMAIN-SUFFIX,linkedin.com,Proxy
DOMAIN-SUFFIX,linode.com,Proxy
DOMAIN-SUFFIX,lithium.com,Proxy
DOMAIN-SUFFIX,littlehj.com,Proxy
DOMAIN-SUFFIX,live.net,Proxy
DOMAIN-SUFFIX,livefilestore.com,Proxy
DOMAIN-SUFFIX,llnwd.net,Proxy
DOMAIN-SUFFIX,logmein.com,Proxy
DOMAIN-SUFFIX,macid.co,Proxy
DOMAIN-SUFFIX,macromedia.com,Proxy
DOMAIN-SUFFIX,macrumors.com,Proxy
DOMAIN-SUFFIX,marketwatch.com,Proxy
DOMAIN-SUFFIX,mashable.com,Proxy
DOMAIN-SUFFIX,mathjax.org,Proxy
DOMAIN-SUFFIX,medium.com,Proxy
DOMAIN-SUFFIX,mega.co.nz,Proxy
DOMAIN-SUFFIX,mega.nz,Proxy
DOMAIN-SUFFIX,megaupload.com,Proxy
DOMAIN-SUFFIX,microsoft.com,Proxy
DOMAIN-SUFFIX,microsofttranslator.com,Proxy
DOMAIN-SUFFIX,mindnode.com,Proxy
DOMAIN-SUFFIX,mlssoccer.com,Proxy
DOMAIN-SUFFIX,mobile01.com,Proxy
DOMAIN-SUFFIX,modmyi.com,Proxy
DOMAIN-SUFFIX,mp3buscador.com,Proxy
DOMAIN-SUFFIX,msedge.net,Proxy
DOMAIN-SUFFIX,mycnnews.com,Proxy
DOMAIN-SUFFIX,myfontastic.com,Proxy
DOMAIN-SUFFIX,name.com,Proxy
DOMAIN-SUFFIX,nasa.gov,Proxy
DOMAIN-SUFFIX,ndr.de,Proxy
DOMAIN-SUFFIX,netdna-cdn.com,Proxy
DOMAIN-SUFFIX,newipnow.com,Proxy
DOMAIN-SUFFIX,nextmedia.com,Proxy
DOMAIN-SUFFIX,nih.gov,Proxy
DOMAIN-SUFFIX,nintendo.com,Proxy
DOMAIN-SUFFIX,nintendo.net,Proxy
DOMAIN-SUFFIX,notion.so,Proxy
DOMAIN-SUFFIX,nrk.no,Proxy
DOMAIN-SUFFIX,nsstatic.net,Proxy
DOMAIN-SUFFIX,nssurge.com,Proxy
DOMAIN-SUFFIX,nyt.com,Proxy
DOMAIN-SUFFIX,nytimes.com,Proxy
DOMAIN-SUFFIX,nytimg.com,Proxy
DOMAIN-SUFFIX,nytstyle.com,Proxy
DOMAIN-SUFFIX,office365.com,Proxy
DOMAIN-SUFFIX,omnigroup.com,Proxy
DOMAIN-SUFFIX,onedrive.com,Proxy
DOMAIN-SUFFIX,onedrive.live.com,Proxy
DOMAIN-SUFFIX,onenote.com,Proxy
DOMAIN-SUFFIX,ooyala.com,Proxy
DOMAIN-SUFFIX,openvpn.net,Proxy
DOMAIN-SUFFIX,openwrt.org,Proxy
DOMAIN-SUFFIX,optimizely.com,Proxy
DOMAIN-SUFFIX,orkut.com,Proxy
DOMAIN-SUFFIX,osha.gov,Proxy
DOMAIN-SUFFIX,osxdaily.com,Proxy
DOMAIN-SUFFIX,ow.ly,Proxy
DOMAIN-SUFFIX,paddle.com,Proxy
DOMAIN-SUFFIX,paddleapi.com,Proxy
DOMAIN-SUFFIX,panoramio.com,Proxy
DOMAIN-SUFFIX,parallels.com,Proxy
DOMAIN-SUFFIX,parse.com,Proxy
DOMAIN-SUFFIX,pdfexpert.com,Proxy
DOMAIN-SUFFIX,periscope.tv,Proxy
DOMAIN-SUFFIX,piaotian.net,Proxy
DOMAIN-SUFFIX,picasaweb.com,Proxy
DOMAIN-SUFFIX,pinboard.in,Proxy
DOMAIN-SUFFIX,pinterest.com,Proxy
DOMAIN-SUFFIX,pixelmator.com,Proxy
DOMAIN-SUFFIX,pixnet.net,Proxy
DOMAIN-SUFFIX,playpcesor.com,Proxy
DOMAIN-SUFFIX,playstation.com,Proxy
DOMAIN-SUFFIX,playstation.com.hk,Proxy
DOMAIN-SUFFIX,playstation.net,Proxy
DOMAIN-SUFFIX,playstationnetwork.com,Proxy
DOMAIN-SUFFIX,pokemon.com,Proxy
DOMAIN-SUFFIX,polymer-project.org,Proxy
DOMAIN-SUFFIX,popo.tw,Proxy
DOMAIN-SUFFIX,prfct.co,Proxy
DOMAIN-SUFFIX,proxfree.com,Proxy
DOMAIN-SUFFIX,psiphon3.com,Proxy
DOMAIN-SUFFIX,ptt.cc,Proxy,force-remote-dns
DOMAIN-SUFFIX,pubu.com.tw,Proxy
DOMAIN-SUFFIX,puffinbrowser.com,Proxy
DOMAIN-SUFFIX,pushwoosh.com,Proxy
DOMAIN-SUFFIX,pximg.net,Proxy
DOMAIN-SUFFIX,readingtimes.com.tw,Proxy
DOMAIN-SUFFIX,readmoo.com,Proxy
DOMAIN-SUFFIX,recaptcha.net,Proxy
DOMAIN-SUFFIX,reuters.com,Proxy
DOMAIN-SUFFIX,rfi.fr,Proxy
DOMAIN-SUFFIX,rileyguide.com,Proxy
DOMAIN-SUFFIX,rime.im,Proxy
DOMAIN-SUFFIX,rsf.org,Proxy
DOMAIN-SUFFIX,sciencedaily.com,Proxy
DOMAIN-SUFFIX,sciencemag.org,Proxy
DOMAIN-SUFFIX,scribd.com,Proxy
DOMAIN-SUFFIX,search.com,Proxy
DOMAIN-SUFFIX,servebom.com,Proxy
DOMAIN-SUFFIX,sfx.ms,Proxy
DOMAIN-SUFFIX,shadowsocks.org,Proxy
DOMAIN-SUFFIX,sharethis.com,Proxy
DOMAIN-SUFFIX,shazam.com,Proxy
DOMAIN-SUFFIX,shutterstock.com,Proxy
DOMAIN-SUFFIX,sidelinesnews.com,Proxy
DOMAIN-SUFFIX,simp.ly,Proxy
DOMAIN-SUFFIX,simplenote.com,Proxy
DOMAIN-SUFFIX,sketchappsources.com,Proxy
DOMAIN-SUFFIX,skype.com,Proxy
DOMAIN-SUFFIX,slack.com,Proxy
DOMAIN-SUFFIX,slack-edge.com,Proxy
DOMAIN-SUFFIX,slack-msgs.com,Proxy
DOMAIN-SUFFIX,slideshare.net,Proxy
DOMAIN-SUFFIX,smartdnsproxy.com,Proxy
DOMAIN-SUFFIX,smartmailcloud.com,Proxy
DOMAIN-SUFFIX,smh.com.au,Proxy
DOMAIN-SUFFIX,snapchat.com,Proxy
DOMAIN-SUFFIX,sndcdn.com,Proxy
DOMAIN-SUFFIX,sockslist.net,Proxy
DOMAIN-SUFFIX,sony.com,Proxy
DOMAIN-SUFFIX,sony.com.hk,Proxy
DOMAIN-SUFFIX,sonyentertainmentnetwork.com,Proxy
DOMAIN-SUFFIX,soundcloud.com,Proxy
DOMAIN-SUFFIX,sourceforge.net,Proxy
DOMAIN-SUFFIX,sowers.org.hk,Proxy
DOMAIN-SUFFIX,speedsmart.net,Proxy
DOMAIN-SUFFIX,spike.com,Proxy
DOMAIN-SUFFIX,spoti.fi,Proxy
DOMAIN-SUFFIX,squarespace.com,Proxy
DOMAIN-SUFFIX,ssa.gov,Proxy
DOMAIN-SUFFIX,sstatic.net,Proxy
DOMAIN-SUFFIX,st.luluku.pw,Proxy
DOMAIN-SUFFIX,stackoverflow.com,Proxy
DOMAIN-SUFFIX,starp2p.com,Proxy
DOMAIN-SUFFIX,startpage.com,Proxy
DOMAIN-SUFFIX,state.gov,Proxy
DOMAIN-SUFFIX,staticflickr.com,Proxy
DOMAIN-SUFFIX,storify.com,Proxy
DOMAIN-SUFFIX,stumbleupon.com,Proxy
DOMAIN-SUFFIX,sugarsync.com,Proxy
DOMAIN-SUFFIX,supermariorun.com,Proxy
DOMAIN-SUFFIX,surfeasy.com.au,Proxy
DOMAIN-SUFFIX,surge.run,Proxy
DOMAIN-SUFFIX,surrenderat20.net,Proxy
DOMAIN-SUFFIX,sydneytoday.com,Proxy
DOMAIN-SUFFIX,symauth.com,Proxy
DOMAIN-SUFFIX,symcb.com,Proxy
DOMAIN-SUFFIX,symcd.com,Proxy
DOMAIN-SUFFIX,t.me,Proxy
DOMAIN-SUFFIX,tablesgenerator.com,Proxy
DOMAIN-SUFFIX,tabtter.jp,Proxy
DOMAIN-SUFFIX,talk853.com,Proxy
DOMAIN-SUFFIX,talkboxapp.com,Proxy
DOMAIN-SUFFIX,talkonly.net,Proxy
DOMAIN-SUFFIX,tapbots.com,Proxy
DOMAIN-SUFFIX,tapbots.net,Proxy
DOMAIN-SUFFIX,tdesktop.com,Proxy
DOMAIN-SUFFIX,teamviewer.com,Proxy
DOMAIN-SUFFIX,techcrunch.com,Proxy
DOMAIN-SUFFIX,technorati.com,Proxy
DOMAIN-SUFFIX,techsmith.com,Proxy
DOMAIN-SUFFIX,telegra.ph,Proxy
DOMAIN-SUFFIX,thebobs.com,Proxy
DOMAIN-SUFFIX,thepiratebay.org,Proxy
DOMAIN-SUFFIX,theverge.com,Proxy
DOMAIN-SUFFIX,thewgo.org,Proxy
DOMAIN-SUFFIX,tiltbrush.com,Proxy
DOMAIN-SUFFIX,tinder.com,Proxy
DOMAIN-SUFFIX,time.com,Proxy
DOMAIN-SUFFIX,timeinc.net,Proxy
DOMAIN-SUFFIX,tiny.cc,Proxy
DOMAIN-SUFFIX,tinychat.com,Proxy
DOMAIN-SUFFIX,tinypic.com,Proxy
DOMAIN-SUFFIX,tmblr.co,Proxy
DOMAIN-SUFFIX,todoist.com,Proxy
DOMAIN-SUFFIX,togetter.com,Proxy
DOMAIN-SUFFIX,tokyocn.com,Proxy
DOMAIN-SUFFIX,tomshardware.com,Proxy
DOMAIN-SUFFIX,torcn.com,Proxy
DOMAIN-SUFFIX,torrentprivacy.com,Proxy
DOMAIN-SUFFIX,torrentproject.se,Proxy
DOMAIN-SUFFIX,torrentz.eu,Proxy
DOMAIN-SUFFIX,traffichaus.com,Proxy
DOMAIN-SUFFIX,transparency.org,Proxy
DOMAIN-SUFFIX,trello.com,Proxy
DOMAIN-SUFFIX,trendsmap.com,Proxy
DOMAIN-SUFFIX,trulyergonomic.com,Proxy
DOMAIN-SUFFIX,trustasiassl.com,Proxy
DOMAIN-SUFFIX,tt-rss.org,Proxy
DOMAIN-SUFFIX,tumblr.co,Proxy
DOMAIN-SUFFIX,tumblr.com,Proxy
DOMAIN-SUFFIX,turbobit.net,Proxy
DOMAIN-SUFFIX,tv.com,Proxy
DOMAIN-SUFFIX,tweetdeck.com,Proxy
DOMAIN-SUFFIX,tweetmarker.net,Proxy
DOMAIN-SUFFIX,twimg.co,Proxy
DOMAIN-SUFFIX,twitch.tv,Proxy
DOMAIN-SUFFIX,twitthat.com,Proxy
DOMAIN-SUFFIX,twtkr.com,Proxy
DOMAIN-SUFFIX,twttr.com,Proxy
DOMAIN-SUFFIX,txmblr.com,Proxy
DOMAIN-SUFFIX,typekit.net,Proxy
DOMAIN-SUFFIX,typography.com,Proxy
DOMAIN-SUFFIX,ubertags.com,Proxy
DOMAIN-SUFFIX,ublock.org,Proxy
DOMAIN-SUFFIX,ubnt.com,Proxy
DOMAIN-SUFFIX,uchicago.edu,Proxy
DOMAIN-SUFFIX,udn.com,Proxy
DOMAIN-SUFFIX,ugo.com,Proxy
DOMAIN-SUFFIX,uhdwallpapers.org,Proxy
DOMAIN-SUFFIX,ulyssesapp.com,Proxy
DOMAIN-SUFFIX,unblockdmm.com,Proxy
DOMAIN-SUFFIX,unblocksites.co,Proxy
DOMAIN-SUFFIX,unpo.org,Proxy
DOMAIN-SUFFIX,untraceable.us,Proxy
DOMAIN-SUFFIX,uploaded.net,Proxy
DOMAIN-SUFFIX,uProxy.org,Proxy
DOMAIN-SUFFIX,urchin.com,Proxy
DOMAIN-SUFFIX,urlparser.com,Proxy
DOMAIN-SUFFIX,us.to,Proxy
DOMAIN-SUFFIX,usertrust.com,Proxy
DOMAIN-SUFFIX,usgs.gov,Proxy
DOMAIN-SUFFIX,usma.edu,Proxy
DOMAIN-SUFFIX,uspto.gov,Proxy
DOMAIN-SUFFIX,ustream.tv,Proxy
DOMAIN-SUFFIX,v.gd,Proxy
DOMAIN-SUFFIX,v2ray.com,Proxy
DOMAIN-SUFFIX,van001.com,Proxy
DOMAIN-SUFFIX,vanpeople.com,Proxy
DOMAIN-SUFFIX,vansky.com,Proxy
DOMAIN-SUFFIX,vbstatic.co,Proxy
DOMAIN-SUFFIX,venchina.com,Proxy
DOMAIN-SUFFIX,venturebeat.com,Proxy
DOMAIN-SUFFIX,veoh.com,Proxy
DOMAIN-SUFFIX,verizonwireless.com,Proxy
DOMAIN-SUFFIX,viber.com,Proxy
DOMAIN-SUFFIX,vid.me,Proxy
DOMAIN-SUFFIX,videomega.tv,Proxy
DOMAIN-SUFFIX,vidinfo.org,Proxy
DOMAIN-SUFFIX,vimeo.com,Proxy
DOMAIN-SUFFIX,vimeocdn.com,Proxy
DOMAIN-SUFFIX,vimperator.org,Proxy
DOMAIN-SUFFIX,vine.co,Proxy
DOMAIN-SUFFIX,visibletweets.com,Proxy
DOMAIN-SUFFIX,vivaldi.com,Proxy
DOMAIN-SUFFIX,voachinese.com,Proxy
DOMAIN-SUFFIX,vocativ.com,Proxy
DOMAIN-SUFFIX,vox-cdn.com,Proxy
DOMAIN-SUFFIX,vpnaccount.org,Proxy
DOMAIN-SUFFIX,vpnbook.com,Proxy
DOMAIN-SUFFIX,vpngate.net,Proxy
DOMAIN-SUFFIX,vsco.co,Proxy
DOMAIN-SUFFIX,vultr.com,Proxy
DOMAIN-SUFFIX,vzw.com,Proxy
DOMAIN-SUFFIX,w.org,Proxy
DOMAIN-SUFFIX,w3schools.com,Proxy
DOMAIN-SUFFIX,wattpad.com,Proxy
DOMAIN-SUFFIX,web2project.net,Proxy
DOMAIN-SUFFIX,webfreer.com,Proxy
DOMAIN-SUFFIX,weblagu.com,Proxy
DOMAIN-SUFFIX,websnapr.com,Proxy
DOMAIN-SUFFIX,webtype.com,Proxy
DOMAIN-SUFFIX,webwarper.net,Proxy
DOMAIN-SUFFIX,wenxuecity.com,Proxy
DOMAIN-SUFFIX,westca.com,Proxy
DOMAIN-SUFFIX,westpoint.edu,Proxy
DOMAIN-SUFFIX,whatbrowser.org,Proxy
DOMAIN-SUFFIX,wikileaks.info,Proxy
DOMAIN-SUFFIX,wikileaks.org,Proxy
DOMAIN-SUFFIX,wikileaks-forum.com,Proxy
DOMAIN-SUFFIX,wikimedia.org,Proxy
DOMAIN-SUFFIX,wikipedia.com,Proxy
DOMAIN-SUFFIX,wikipedia.org,Proxy
DOMAIN-SUFFIX,windows.com,Proxy
DOMAIN-SUFFIX,windows.net,Proxy
DOMAIN-SUFFIX,wn.com,Proxy
DOMAIN-SUFFIX,wordpress.com,Proxy
DOMAIN-SUFFIX,workflow.is,Proxy
DOMAIN-SUFFIX,worldcat.org,Proxy
DOMAIN-SUFFIX,wow.com,Proxy
DOMAIN-SUFFIX,wp.com,Proxy
DOMAIN-SUFFIX,wsj.com,Proxy
DOMAIN-SUFFIX,wsj.net,Proxy
DOMAIN-SUFFIX,wwitv.com,Proxy
DOMAIN-SUFFIX,xanga.com,Proxy
DOMAIN-SUFFIX,xda-developers.com,Proxy
DOMAIN-SUFFIX,xeeno.com,Proxy
DOMAIN-SUFFIX,xiti.com,Proxy
DOMAIN-SUFFIX,xn--h5qz41fzgdxxl.com,Proxy
DOMAIN-SUFFIX,xuite.net,Proxy
DOMAIN-SUFFIX,yahoo.com,Proxy
DOMAIN-SUFFIX,yahooapis.com,Proxy
DOMAIN-SUFFIX,yasni.co.uk,Proxy
DOMAIN-SUFFIX,yastatic.net,Proxy
DOMAIN-SUFFIX,yeeyi.com,Proxy
DOMAIN-SUFFIX,yesasia.com,Proxy
DOMAIN-SUFFIX,yes-news.com,Proxy
DOMAIN-SUFFIX,yidio.com,Proxy
DOMAIN-SUFFIX,yimg.com,Proxy
DOMAIN-SUFFIX,ying.com,Proxy
DOMAIN-SUFFIX,yorkbbs.ca,Proxy
DOMAIN-SUFFIX,youmaker.com,Proxy
DOMAIN-SUFFIX,yourlisten.com,Proxy
DOMAIN-SUFFIX,youtu.be,Proxy
DOMAIN-SUFFIX,yoyo.org,Proxy
DOMAIN-SUFFIX,ytimg.com,Proxy
DOMAIN-SUFFIX,zacebook.com,Proxy
DOMAIN-SUFFIX,zalmos.com,Proxy
DOMAIN-SUFFIX,zaobao.com.sg,Proxy
DOMAIN-SUFFIX,zeutch.com,Proxy
DOMAIN-SUFFIX,zynamics.com,Proxy

// LINE
IP-CIDR,103.2.28.0/22,Proxy,no-resolve
IP-CIDR,119.235.224.0/21,Proxy,no-resolve
IP-CIDR,119.235.232.0/23,Proxy,no-resolve
IP-CIDR,119.235.235.0/24,Proxy,no-resolve
IP-CIDR,119.235.236.0/23,Proxy,no-resolve
IP-CIDR,125.6.146.0/24,Proxy,no-resolve
IP-CIDR,125.6.149.0/24,Proxy,no-resolve
IP-CIDR,125.6.190.0/24,Proxy,no-resolve
IP-CIDR,203.104.103.0/24,Proxy,no-resolve
IP-CIDR,203.104.128.0/20,Proxy,no-resolve
IP-CIDR,203.174.66.64/26,Proxy,no-resolve
IP-CIDR,203.174.77.0/24,Proxy,no-resolve

// Telegram
IP-CIDR,109.239.140.0/24,Proxy,no-resolve
IP-CIDR,149.154.160.0/20,Proxy,no-resolve
IP-CIDR,91.108.4.0/16,Proxy,no-resolve
IP-CIDR6,2001:67c:4e8::/48,REJECT,no-resolve
IP-CIDR6,2001:b28:f23d::/48,REJECT,no-resolve
IP-CIDR6,2001:b28:f23f::/48,REJECT,no-resolve

// Kakao Talk
IP-CIDR,1.201.0.0/24,Proxy,no-resolve
IP-CIDR,103.246.56.0/22,Proxy,no-resolve
IP-CIDR,103.27.148.0/22,Proxy,no-resolve
IP-CIDR,110.76.140.0/22,Proxy,no-resolve
IP-CIDR,113.61.104.0/22,Proxy,no-resolve
IP-CIDR,27.0.236.0/22,Proxy,no-resolve



// Client
PROCESS-NAME,Paws for Trello,Domestic
PROCESS-NAME,Thunder,Domestic
PROCESS-NAME,trustd,Domestic
PROCESS-NAME,WeChat,Domestic

// UA
USER-AGENT,%E5%8D%B3%E5%88%BB*,Domestic
USER-AGENT,*Vainglory* ,Domestic
USER-AGENT,AdBlock*,Domestic
USER-AGENT,arrowio*,Domestic
USER-AGENT,balls*,Domestic
USER-AGENT,cmblife*,Domestic
USER-AGENT,hide*,Domestic
USER-AGENT,MegaWerewolf*,Domestic
USER-AGENT,MicroMessenger*,Domestic
USER-AGENT,Moke*,Domestic
USER-AGENT,osee2unifiedRelease*,Domestic
USER-AGENT,QQ*,Domestic
USER-AGENT,TeamViewer*,Domestic
USER-AGENT,TIM*,Domestic
USER-AGENT,WeChat*,Domestic



# DIRECT

// Spark
DOMAIN-SUFFIX,api.amplitude.com,Domestic
DOMAIN-SUFFIX,app.smartmailcloud.com,Domestic
DOMAIN-SUFFIX,firebaseio.com,Domestic
DOMAIN-SUFFIX,gate.hockeyapp.net,Domestic

DOMAIN-SUFFIX,12306.com,Domestic
DOMAIN-SUFFIX,126.net,Domestic
DOMAIN-SUFFIX,163.com,Domestic
DOMAIN-SUFFIX,360.cn,Domestic
DOMAIN-SUFFIX,360.com,Domestic
DOMAIN-SUFFIX,360buy.com,Domestic
DOMAIN-SUFFIX,360buyimg.com,Domestic
DOMAIN-SUFFIX,36kr.com,Domestic
DOMAIN-SUFFIX,58.com,Domestic
DOMAIN-SUFFIX,abercrombie.com,Domestic
DOMAIN-SUFFIX,acfun.tv,Domestic
DOMAIN-SUFFIX,acgvideo.com,Domestic
DOMAIN-SUFFIX,adobesc.com,Domestic
DOMAIN-SUFFIX,air-matters.com,Domestic
DOMAIN-SUFFIX,air-matters.io,Domestic
DOMAIN-SUFFIX,aixifan.com,Domestic
DOMAIN-SUFFIX,akadns.net,Domestic
DOMAIN-SUFFIX,alicdn.com,Domestic
DOMAIN-SUFFIX,alipay.com,Domestic
DOMAIN-SUFFIX,alipayobjects.com,Domestic
DOMAIN-SUFFIX,aliyun.com,Domestic
DOMAIN-SUFFIX,amap.com,Domestic
DOMAIN-SUFFIX,analytics.126.net,Domestic
DOMAIN-SUFFIX,apache.org,Domestic
DOMAIN-SUFFIX,appstore.com,Domestic
DOMAIN-SUFFIX,autonavi.com,Domestic
DOMAIN-SUFFIX,bababian.com,Domestic
DOMAIN-SUFFIX,baidu.com,Domestic
DOMAIN-SUFFIX,battle.net,Domestic
DOMAIN-SUFFIX,bdimg.com,Domestic
DOMAIN-SUFFIX,bdstatic.com,Domestic
DOMAIN-SUFFIX,beatsbydre.com,Domestic
DOMAIN-SUFFIX,bilibili.cn,Domestic
DOMAIN-SUFFIX,bilibili.com,Domestic
DOMAIN-SUFFIX,bing.com,Domestic
DOMAIN-SUFFIX,caiyunapp.com,Domestic
DOMAIN-SUFFIX,ccgslb.com,Domestic
DOMAIN-SUFFIX,ccgslb.net,Domestic
DOMAIN-SUFFIX,chinacache.net,Domestic
DOMAIN-SUFFIX,chunbo.com,Domestic
DOMAIN-SUFFIX,chunboimg.com,Domestic
DOMAIN-SUFFIX,clashroyaleapp.com,Domestic
DOMAIN-SUFFIX,clouddn.com,Domestic
DOMAIN-SUFFIX,cmfu.com,Domestic
DOMAIN-SUFFIX,cnbeta.com,Domestic
DOMAIN-SUFFIX,cnbetacdn.com,Domestic
DOMAIN-SUFFIX,conoha.jp,Domestic
DOMAIN-SUFFIX,culturedcode.com,Domestic
DOMAIN-SUFFIX,didialift.com,Domestic
DOMAIN-SUFFIX,douban.com,Domestic
DOMAIN-SUFFIX,doubanio.com,Domestic
DOMAIN-SUFFIX,douyu.com,Domestic
DOMAIN-SUFFIX,douyu.tv,Domestic
DOMAIN-SUFFIX,douyutv.com,Domestic
DOMAIN-SUFFIX,duokan.com,Domestic
DOMAIN-SUFFIX,duoshuo.com,Domestic
DOMAIN-SUFFIX,dytt8.net,Domestic
DOMAIN-SUFFIX,easou.com,Domestic
DOMAIN-SUFFIX,ecitic.com,Domestic
DOMAIN-SUFFIX,ecitic.net,Domestic
DOMAIN-SUFFIX,eclipse.org,Domestic
DOMAIN-SUFFIX,eudic.net,Domestic
DOMAIN-SUFFIX,ewqcxz.com,Domestic
DOMAIN-SUFFIX,exmail.qq.com,Domestic
DOMAIN-SUFFIX,feng.com,Domestic
DOMAIN-SUFFIX,fir.im,Domestic
DOMAIN-SUFFIX,frdic.com,Domestic
DOMAIN-SUFFIX,fresh-ideas.cc,Domestic
DOMAIN-SUFFIX,geetest.com,Domestic
DOMAIN-SUFFIX,godic.net,Domestic
DOMAIN-SUFFIX,goodread.com,Domestic
DOMAIN-SUFFIX,google.cn,Domestic
DOMAIN-SUFFIX,gtimg.com,Domestic
DOMAIN-SUFFIX,haibian.com,Domestic
DOMAIN-SUFFIX,hao123.com,Domestic
DOMAIN-SUFFIX,haosou.com,Domestic
DOMAIN-SUFFIX,hdslb.com,Domestic
DOMAIN-SUFFIX,hdslb.net,Domestic
DOMAIN-SUFFIX,hollisterco.com,Domestic
DOMAIN-SUFFIX,hongxiu.com,Domestic
DOMAIN-SUFFIX,hxcdn.net,Domestic
DOMAIN-SUFFIX,iciba.com,Domestic
DOMAIN-SUFFIX,icloud.com,Domestic
DOMAIN-SUFFIX,ifeng.com,Domestic
DOMAIN-SUFFIX,ifengimg.com,Domestic
DOMAIN-SUFFIX,images-amazon.com,Domestic
DOMAIN-SUFFIX,ipip.net,Domestic
DOMAIN-SUFFIX,iqiyi.com,Domestic
DOMAIN-SUFFIX,ithome.com,Domestic
DOMAIN-SUFFIX,ixdzs.com,Domestic
DOMAIN-SUFFIX,jd.com,Domestic
DOMAIN-SUFFIX,jd.hk,Domestic
DOMAIN-SUFFIX,jianshu.com,Domestic
DOMAIN-SUFFIX,jianshu.io,Domestic
DOMAIN-SUFFIX,jianshuapi.com,Domestic
DOMAIN-SUFFIX,jiathis.com,Domestic
DOMAIN-SUFFIX,jomodns.com,Domestic
DOMAIN-SUFFIX,knewone.com,Domestic
DOMAIN-SUFFIX,kuaidi100.com,Domestic
DOMAIN-SUFFIX,lecloud.com,Domestic
DOMAIN-SUFFIX,lemicp.com,Domestic
DOMAIN-SUFFIX,letv.com,Domestic
DOMAIN-SUFFIX,letvcloud.com,Domestic
DOMAIN-SUFFIX,live.com,Domestic
DOMAIN-SUFFIX,lizhi.io,Domestic
DOMAIN-SUFFIX,localizecdn.com,Domestic
DOMAIN-SUFFIX,lucifr.com,Domestic
DOMAIN-SUFFIX,luoo.net,Domestic
DOMAIN-SUFFIX,lxdns.com,Domestic
DOMAIN-SUFFIX,maven.org,Domestic
DOMAIN-SUFFIX,meizu.com,Domestic
DOMAIN-SUFFIX,mi.com,Domestic
DOMAIN-SUFFIX,miaopai.com,Domestic
DOMAIN-SUFFIX,miui.com,Domestic
DOMAIN-SUFFIX,miwifi.com,Domestic
DOMAIN-SUFFIX,mob.com,Domestic
DOMAIN-SUFFIX,moke.com,Domestic
DOMAIN-SUFFIX,mxhichina.com,Domestic
DOMAIN-SUFFIX,myqcloud.com,Domestic
DOMAIN-SUFFIX,myunlu.com,Domestic
DOMAIN-SUFFIX,netease.com,Domestic
DOMAIN-SUFFIX,nssurge.com,Domestic
DOMAIN-SUFFIX,nuomi.com,Domestic
DOMAIN-SUFFIX,ourdvs.com,Domestic
DOMAIN-SUFFIX,outlook.com,Domestic
DOMAIN-SUFFIX,overcast.fm,Domestic
DOMAIN-SUFFIX,paypal.com,Domestic
DOMAIN-SUFFIX,pgyer.com,Domestic
DOMAIN-SUFFIX,pstatp.com,Domestic
DOMAIN-SUFFIX,qbox.me,Domestic
DOMAIN-SUFFIX,qcloud.com,Domestic
DOMAIN-SUFFIX,qdaily.com,Domestic
DOMAIN-SUFFIX,qdmm.com,Domestic
DOMAIN-SUFFIX,qhimg.com,Domestic
DOMAIN-SUFFIX,qidian.com,Domestic
DOMAIN-SUFFIX,qihucdn.com,Domestic
DOMAIN-SUFFIX,qin.io,Domestic
DOMAIN-SUFFIX,qingmang.me,Domestic
DOMAIN-SUFFIX,qingmang.mobi,Domestic
DOMAIN-SUFFIX,qiniucdn.com,Domestic
DOMAIN-SUFFIX,qiniudn.com,Domestic
DOMAIN-SUFFIX,qiyi.com,Domestic
DOMAIN-SUFFIX,qiyipic.com,Domestic
DOMAIN-SUFFIX,qq.com,Domestic
DOMAIN-SUFFIX,qqurl.com,Domestic
DOMAIN-SUFFIX,rarbg.to,Domestic
DOMAIN-SUFFIX,rrmj.tv,Domestic
DOMAIN-SUFFIX,ruguoapp.com,Domestic
DOMAIN-SUFFIX,sandai.net,Domestic
DOMAIN-SUFFIX,sinaapp.com,Domestic
DOMAIN-SUFFIX,sinaimg.cn,Domestic
DOMAIN-SUFFIX,sinaimg.com,Domestic
DOMAIN-SUFFIX,smzdm.com,Domestic
DOMAIN-SUFFIX,snwx.com,Domestic
DOMAIN-SUFFIX,so.com,Domestic
DOMAIN-SUFFIX,sogou.com,Domestic
DOMAIN-SUFFIX,sogoucdn.com,Domestic
DOMAIN-SUFFIX,sohu.com,Domestic
DOMAIN-SUFFIX,soku.com,Domestic
DOMAIN-SUFFIX,soso.com,Domestic
DOMAIN-SUFFIX,speedtest.net,Domestic
DOMAIN-SUFFIX,sspai.com,Domestic
DOMAIN-SUFFIX,startssl.com,Domestic
DOMAIN-SUFFIX,store.steampowered.com,Domestic
DOMAIN-SUFFIX,suning.com,Domestic
DOMAIN-SUFFIX,symcd.com,Domestic
DOMAIN-SUFFIX,taobao.com,Domestic
DOMAIN-SUFFIX,tenpay.com,Domestic
DOMAIN-SUFFIX,tietuku.com,Domestic
DOMAIN-SUFFIX,tmall.com,Domestic
DOMAIN-SUFFIX,trello.com,Domestic
DOMAIN-SUFFIX,trellocdn.com,Domestic
DOMAIN-SUFFIX,ttmeiju.com,Domestic
DOMAIN-SUFFIX,tudou.com,Domestic
DOMAIN-SUFFIX,udache.com,Domestic
DOMAIN-SUFFIX,umengcloud.com,Domestic
DOMAIN-SUFFIX,upaiyun.com,Domestic
DOMAIN-SUFFIX,upyun.com,Domestic
DOMAIN-SUFFIX,uxengine.net,Domestic
DOMAIN-SUFFIX,v2ex.co,Domestic
DOMAIN-SUFFIX,v2ex.com,Domestic
DOMAIN-SUFFIX,vultr.com,Domestic
DOMAIN-SUFFIX,wandoujia.com,Domestic
DOMAIN-SUFFIX,weather.com,Domestic
DOMAIN-SUFFIX,weibo.cn,Domestic
DOMAIN-SUFFIX,weibo.com,Domestic
DOMAIN-SUFFIX,weico.cc,Domestic
DOMAIN-SUFFIX,weiphone.com,Domestic
DOMAIN-SUFFIX,weiphone.net,Domestic
DOMAIN-SUFFIX,windowsupdate.com,Domestic
DOMAIN-SUFFIX,workflowy.com,Domestic
DOMAIN-SUFFIX,xclient.info,Domestic
DOMAIN-SUFFIX,xdrig.com,Domestic
DOMAIN-SUFFIX,xiami.com,Domestic
DOMAIN-SUFFIX,xiami.net,Domestic
DOMAIN-SUFFIX,xiaojukeji.com,Domestic
DOMAIN-SUFFIX,xiaomi.com,Domestic
DOMAIN-SUFFIX,xiaomi.net,Domestic
DOMAIN-SUFFIX,xiaomicp.com,Domestic
DOMAIN-SUFFIX,ximalaya.com,Domestic
DOMAIN-SUFFIX,xitek.com,Domestic
DOMAIN-SUFFIX,xmcdn.com,Domestic
DOMAIN-SUFFIX,xslb.net,Domestic
DOMAIN-SUFFIX,xunlei.com,Domestic
DOMAIN-SUFFIX,yach.me,Domestic
DOMAIN-SUFFIX,yeepay.com,Domestic
DOMAIN-SUFFIX,yhd.com,Domestic
DOMAIN-SUFFIX,yinxiang.com,Domestic
DOMAIN-SUFFIX,yixia.com,Domestic
DOMAIN-SUFFIX,ykimg.com,Domestic
DOMAIN-SUFFIX,youdao.com,Domestic
DOMAIN-SUFFIX,youku.com,Domestic
DOMAIN-SUFFIX,yunjiasu-cdn.net,Domestic
DOMAIN-SUFFIX,zealer.com,Domestic
DOMAIN-SUFFIX,zgslb.net,Domestic
DOMAIN-SUFFIX,zhihu.com,Domestic
DOMAIN-SUFFIX,zhimg.com,Domestic
DOMAIN-SUFFIX,zimuzu.tv,Domestic

// TeamViewer
IP-CIDR,109.239.140.0/24,Domestic,no-resolve



DOMAIN-SUFFIX,cn,Domestic

// Accelerate direct sites
DOMAIN-KEYWORD,torrent,Domestic

// Force some domains which are fucked by GFW while resolving DNS,or do not respect the system Proxy
DOMAIN-KEYWORD,appledaily,Proxy,force-remote-dns
DOMAIN-KEYWORD,blogspot,Proxy,force-remote-dns
DOMAIN-KEYWORD,dropbox,Proxy,force-remote-dns
DOMAIN-KEYWORD,google,Proxy,force-remote-dns
DOMAIN-KEYWORD,spotify,Proxy,force-remote-dns
DOMAIN-KEYWORD,telegram,Proxy,force-remote-dns
DOMAIN-KEYWORD,whatsapp,Proxy,force-remote-dns
DOMAIN-SUFFIX,1e100.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,2mdn.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,abc.xyz,Proxy,force-remote-dns
DOMAIN-SUFFIX,akamai.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,appspot.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,autodraw.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,bandwagonhost.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,blogblog.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,cdninstagram.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,chromeexperiments.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,creativelab5.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,crittercism.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,culturalspot.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,dartlang.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,facebook.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,facebook.design,Proxy,force-remote-dns
DOMAIN-SUFFIX,facebook.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,fb.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,fb.me,Proxy,force-remote-dns
DOMAIN-SUFFIX,fbcdn.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,fbsbx.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,gcr.io,Proxy,force-remote-dns
DOMAIN-SUFFIX,gmail.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,googleapis.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,googlevideo.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,gosetsuden.jp,Proxy,force-remote-dns
DOMAIN-SUFFIX,gvt1.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,gwtproject.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,heroku.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,html5rocks.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,humblebundle.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,instagram.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,keyhole.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,kobo.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,kobobooks.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,madewithcode.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,material.io,Proxy,force-remote-dns
DOMAIN-SUFFIX,messenger.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,netmarble.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,nianticlabs.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,pinimg.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,pixiv.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,pubnub.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,scdn.co,Proxy,force-remote-dns
DOMAIN-SUFFIX,t.co,Proxy,force-remote-dns
DOMAIN-SUFFIX,telegram.me,Proxy,force-remote-dns
DOMAIN-SUFFIX,tensorflow.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,thefacebook.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,toggleable.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,torproject.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,twimg.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twitpic.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,twitter.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,unfiltered.news,Proxy,force-remote-dns
DOMAIN-SUFFIX,waveprotocol.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,webmproject.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,webrtc.org,Proxy,force-remote-dns
DOMAIN-SUFFIX,whatsapp.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,whatsapp.net,Proxy,force-remote-dns
DOMAIN-SUFFIX,youtube.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,youtube-nocookie.com,Proxy,force-remote-dns

// LAN,debugging rules should place above this line
IP-CIDR,10.0.0.0/8,DIRECT
IP-CIDR,100.64.0.0/10,DIRECT
IP-CIDR,127.0.0.0/8,DIRECT
IP-CIDR,172.0.0.0/12,DIRECT
IP-CIDR,192.168.0.0/16,DIRECT

// Detect local network
GEOIP,CN,Domestic
// Use Proxy for all others
FINAL,Others

[Host]


localhost = 127.0.0.1
syria.sy = 127.0.0.1

thisisinsider.com = server:8.8.4.4

onedrive.live.com = 204.79.197.217
skyapi.onedrive.live.com = 131.253.14.230

*.alicdn.com = server:223.5.5.5
*.jd.com = server:223.5.5.5
*.taobao.com = server:223.5.5.5
*.tmall.com = server:223.5.5.5

[URL Rewrite]


// Google_Service_HTTPS_Jump
^https?://(www.)?g.cn https://www.google.com 302
^https?://(www.)?google.cn https://www.google.com 302

// Anti_ISP_JD_Hijack
^https?://coupon.m.jd.com/ https://coupon.m.jd.com/ 302
^https?://h5.m.jd.com/ https://h5.m.jd.com/ 302
^https?://item.m.jd.com/ https://item.m.jd.com/ 302
^https?://m.jd.com https://m.jd.com 302
^https?://newcz.m.jd.com/ https://newcz.m.jd.com/ 302
^https?://p.m.jd.com/ https://p.m.jd.com/ 302
^https?://so.m.jd.com/ https://so.m.jd.com/ 302
^https?://union.click.jd.com/jda? http://union.click.jd.com/jda?adblock= header
^https?://union.click.jd.com/sem.php? http://union.click.jd.com/sem.php?adblock= header
^https?://www.jd.com/ https://www.jd.com/ 302

// Anti_ISP_Taobao_Hijack
^https?://m.taobao.com/ https://m.taobao.com/ 302

// Wiki
^https?://.+.(m.)?wikipedia.org/wiki http://www.wikiwand.com/en 302
^https?://zh.(m.)?wikipedia.org/(zh-hans|zh-sg|zh-cn|zh(?=/)) http://www.wikiwand.com/zh 302
^https?://zh.(m.)?wikipedia.org/zh-[a-zA-Z]{2,} http://www.wikiwand.com/zh-hant 302

// Other
^https?://cfg.m.ttkvod.com/mobile/ttk_mobile_1.8.txt http://ogtre5vp0.bkt.clouddn.com/Static/TXT/ttk_mobile_1.8.txt header
^https?://cnzz.com/ http://ogtre5vp0.bkt.clouddn.com/background.png? header
^https?://issuecdn.baidupcs.com/issue/netdisk/guanggao/ http://ogtre5vp0.bkt.clouddn.com/background.png? header
^https?://m.qu.la/stylewap/js/wap.js http://ogtre5vp0.bkt.clouddn.com/qu_la_wap.js 302
^https?://m.yhd.com/1/? http://m.yhd.com/1/?adbock= 302
^https?://n.mark.letv.com/m3u8api/ http://burpsuite.applinzi.com/Interface header
^https?://sqimg.qq.com/ https://sqimg.qq.com/ 302
^https?://static.m.ttkvod.com/static_cahce/index/index.txt http://ogtre5vp0.bkt.clouddn.com/Static/TXT/index.txt header
^https?://www.iqshw.com/d/js/m http://burpsuite.applinzi.com/Interface header
^https?://www.iqshw.com/d/js/m http://rewrite.websocket.site:10/Other/Static/JS/Package.js? header

// Anti_ISP_JavaScript_Injection
^https?://101.251.211.235 - reject
^https?://103.249.254.113 - reject
^https?://106.75.65.92 - reject
^https?://120.132.57.41 - reject
^https?://120.132.63.203 - reject
^https?://120.26.151.246 - reject
^https?://120.55.199.139 - reject
^https?://120.76.189.132 - reject
^https?://122.226.223.163 - reject
^https?://139.196.239.52 - reject
^https?://180.76.155.58 - reject
^https?://183.131.79.30 - reject
^https?://211.155.94.198 - reject
^https?://223.6.255.99 - reject
^https?://c.minisplat.cn/ - reject
^https?://c1.minisplat.cn/ - reject
^https?://cache.changjingyi.cn/ - reject
^https?://cache.gclick.cn/ - reject

// Anti_ISP_Safari_Baidu_CPM_Hijack
^https?://m.coolaiy.com/b.php - reject
^https?://www.babyye.com/b.php - reject
^https?://www.gwv7.com/b.php - reject
^https?://www.likeji.net/b.php - reject

// Tencent
^https?://imgcache.qq.com/qqlive/ - reject
^https?://mi.gdt.qq.com\/gdt_mview.fcg\?posid= - reject
^https?://mp.weixin.qq.com/mp/report - reject
^https?://news.l.qq.com\/app\? - reject
^https?://r.inews.qq.com/adsBlacklist - reject
^https?://r.inews.qq.com/getBannerAds - reject
^https?://r.inews.qq.com/getFullScreenPic - reject
^https?://r.inews.qq.com/getNewsRemoteConfig - reject
^https?://r.inews.qq.com/getSplash\?apptype=ios\&startarticleid=\&__qnr= - reject
^https?://r.inews.qq.com/searchHotCatList - reject
^https?://r.inews.qq.com/upLoadLoc - reject

// Moji
^https?://ad.api.moji.com/ad/log/stat - reject
^https?://ast.api.moji.com/assist/ad/moji/stat - reject
^https?://cdn.moji.com/adlink/avatarcard - reject
^https?://cdn.moji.com/adlink/common - reject
^https?://cdn.moji.com/adlink/splash/ - reject
^https?://cdn.moji.com/advert/ - reject
^https?://cdn2.moji002.com/webpush/ad2/ - reject
^https?://fds.api.moji.com/card/recommend - reject
^https?://show.api.moji.com/json/showcase/getAll - reject
^https?://stat.moji.com - reject
^https?://storage.360buyimg.com/kepler-app - reject
^https?://ugc.moji001.com/sns/json/profile/get_unread - reject

// Youku

^https?://ad.api.3g.youku.com - reject
^https?://api.appsdk.soku.com/bg/r - reject
^https?://api.appsdk.soku.com/tag/r - reject
^https?://api.k.sohu.com/api/channel/ad/ - reject
^https?://api.mobile.youku.com/adv/ - reject
^https?://api.mobile.youku.com/layout/search/hot/word - reject
^https?://hd.api.mobile.youku.com/common/v3/hudong/new - reject
^https?://hd.mobile.youku.com/common/v3/hudong/new - reject
^https?://k.youku.com/player/getFlvPath/ - reject
^https?://m.youku.com/video/libs/iwt.js - reject
^https?://pic.k.sohu.com/img8/wb/tj/ - reject
^https?://r.l.youku.com/rec_at_click - reject
^https?://r1.ykimg.com/\w{30,35}.jpg - reject
^https?://r1.ykimg.com/material/.+/\d{3,4}-\d{4} - reject
^https?://r1.ykimg.com/material/.+/\d{6}/\d{4}/ - reject

// ZhuiShu
^https?://api.zhuishushenqi.com/advert - reject
^https?://api.zhuishushenqi.com/notification/shelfMessage - reject
^https?://api.zhuishushenqi.com/recommend - reject
^https?://api.zhuishushenqi.com/splashes/ios - reject
^https?://mi.gdt.qq.com/gdt_mview.fcg - reject
// Update
^https?://api.zhuishushenqi.com/user/bookshelf-updated - reject
^https?://itunes.apple.com/lookup\?id=575826903 - reject

// IQIYI
^https?://.+/cdn/qiyiapp/\d{8}/.+&dis_dz= - reject
^https?://.+/cdn/qiyiapp/\d{8}/.+&z=\w - reject

// ChinaRailcom
^https?://211.98.70.226:8080/ - reject
^https?://211.98.71.195:8080/ - reject
^https?://211.98.71.196:8080/ - reject

// Sohu
^https?://agn.aty.sohu.com/m? - reject
^https?://api.tv.sohu.com/mobile/control/switch.json? - reject
^https?://api.tv.sohu.com/mobile_user/device/clientconf.json? - reject
^https?://api.tv.sohu.com/mobile_user/push/uploadtoken.json? - reject
^https?://api.tv.sohu.com/v4/mobile/albumdetail.json? - reject
^https?://api.tv.sohu.com/v4/mobile/albumdetail.json\?poid= - reject
^https?://api.tv.sohu.com/v4/mobile/control/switch.json? - reject
^https?://hui.sohu.com/predownload2/? - reject
^https?://m.aty.sohu.com/openload? - reject
^https?://mbl.56.com/config/v1/common/config.union.ios.do? - reject
^https?://mmg.aty.sohu.com/mqs? - reject
^https?://mmg.aty.sohu.com/pvlog? - reject
^https?://photocdn.sohu.com/tvmobilemvms - reject
^https?://s.go.sohu.com/adgtr/\?gbcode= - reject
^https?://s1.api.tv.itc.cn/v4/mobile/feeling/list.json - reject
^https?://s1.api.tv.itc.cn/v4/mobile/searchFunctionConfig/list.json - reject

// Zhihu
^https?://api.zhihu.com/answers/\d+?/comments/featured-comment-ad - reject
^https?://api.zhihu.com/launch - reject
^https?://api.zhihu.com/real_time_launch - reject

// Baidu
(ps|sv|offnavi|newvector|ulog\.imap|newloc)(\.map)?\.(baidu|n\.shifen)\.com - reject
^https?://afd.baidu.com/afd/entry - reject
^https?://als.baidu.com/clog/clog - reject
^https?://baichuan.baidu.com/rs/adpmobile/launch - reject
^https?://bj.bcebos.com/fc-feed/0/pic/ - reject
^https?://c.tieba.baidu.com/c/p/img\?src= - reject
^https?://c.tieba.baidu.com/c/s/logtogether\?cmd= - reject
^https?://gss0.bdstatic.com/.+/static/wiseindex/img/bd_red_packet.png - reject
^https?://imgsrc.baidu.com\/forum\/pic\/item/ - reject
^https?://sm.domobcdn.com/ugc/\w/ - reject
^https?://tb1.bdstatic.com/tb/cms/ngmis/adsense/*.jpg - reject
^https?://tb2.bdstatic.com/tb/mobile/spb/widget/jump - reject

// Baidu Wenku
^https?://wapwenku.baidu.com/view/fengchao/ - reject
^https?://wapwenku.baidu.com/view/fengchaoTwojump/ - reject
^https?://wenku.baidu.com/shifen/ - reject

// 10086
^.+/gmccapp/file/image/preloading/preloading\d{17}.jpg - reject
^https?://\w{2}.10086.cn/gmccapp/file/image/homepage/adver - reject
^https?://\w{2}.10086.cn/upfile/khd/loadingpage/ - reject
^https?://app.10086.cn/group - reject
^https?://app.m.zj.chinamobile.com/zjweb/SpAdvert - reject
^https?://mbusihall.sh.chinamobile.com:\d{4}/upload/v4/img/homePage/ - reject

// 10000
^https?://image1.chinatelecom-ec.com/images/.+/\d{13}.jpg - reject

// 10010
^https://\w{11,12}.wo.com.cn - reject
^https?://m.client.10010.com/mobileService/activity/get_client_adv - reject
^https?://m.client.10010.com/mobileService/activity/get_startadv - reject
^https?://m1.ad.10010.com/noticeMag/images/imageUpload/2\d{3} - reject
^https?://res.mall.10010.cn/mall/common/js/fa.js?referer= - reject

// Ifeng
^https?://api.newad.ifeng.com/ClientAdversApi1508\?adids= - reject
^https?://exp.3g.ifeng.com/coverAdversApi\?gv=. - reject
^https?://ifengad.3g.ifeng.com/ad/pv.php\?stat= - reject
^https?://iis1.deliver.ifeng.com/getmcode\?adid= - reject

// 163
^https?://mimg.127.net/external/smartpop-manger.min.js - reject

// 163 Music
^https?://.+/eapi/ad/ - reject
^https?://.+?163.com/madr?app=\b.+platform=\b.+uid - reject
^https?://dongfeng.alicdn.com/.+\d{4}y\d{3} - reject
^https?://iadmat.nosdn.127.net/ad - reject
^https?://iadmatvideo.nosdn.127.net/ad - reject
^https?://haitaoad.nosdn.127.net/ad - reject
^https?://music.163.com/eapi/ad/ - reject

// JD
^https?://111.13.29.201/client.action\?functionId=start - reject
^https?://api.m.jd.com/client.action\?functionId=start - reject
^https?://m.360buyimg.com/mobilecms/s640x1136_jfs/ - reject

// JDjr
^https?://ms.jr.jd.com/gw/generic/base/na/m/adInfo - reject

// Taobao
^https?://gw.alicdn.com/tfs/.+-1125-1602 - reject

// Douban
^https?://(\d{1,3}\.){1,3}\d{1,3}/view/dale-online/dale_ad/ - reject
^https?://img\d.doubanio.com/rda - reject
^https?://img\d.doubanio.com/view/dale-online/dale_ad/ - reject

// Doubandianying
^https?://api.douban.com/v2/app_ads/common_ads - reject
^https?://frodo.douban.com/api/v2/movie/banner - reject

// Douyu
^https?://capi.douyucdn.cn/lapi/sign/appapi/getinfo - reject
^https?://capi.douyucdn.cn/api/v1/getStartSend - reject
^https?://douyucdn.cn/.+/appapi/getinfo - reject
^https?://staticlive.douyucdn.cn/.+/getStartSend - reject
^https?://staticlive.douyucdn.cn/upload/signs/ - reject

// ele
^https?://elemecdn.com/.+/sitemap - reject
^https?://fuss10.elemecdn.com/.+/w/640/h/1136 - reject
^https?://m.elecfans.com/static/js/ad.js - reject
^https?://www1.elecfans.com/www/delivery/ - reject

// ios.win007
^https?://ios.win007.com/Phone/images - reject

// Toutiao
^https?://p\d{1}.pstatp.com/origin - reject
^https?://pb\d{1}.pstatp.com/origin - reject

// Weibo
^https?://simg.s.weibo.com/.+_ios\d{2}.gif - reject
^https?://sdkapp.uve.weibo.com/interface/sdk/sdkad.php - reject
^https?://u1.img.mobile.sina.cn/public/files/image/\d{3}x\d{2,4} - reject

// Xianyu
^https?://gw.alicdn.com/mt/ - reject
^https?://gw.alicdn.com/tfs/.+\d{3,4}-\d{4} - reject
^https?://gw.alicdn.com/tps/.+\d{3,4}-\d{4} - reject

// Ximalaya
^https?://adse.+\.com\/[a-z]{4}\/loading\?appid= - reject
^https?://adse.ximalaya.com\/ting\/feed\?appid= - reject
^https?://adse.ximalaya.com\/ting\/loading\?appid= - reject
^https?://adse.ximalaya.com\/ting\?appid= - reject
^https?://fdfs.xmcdn.com/group21/M03/E7/3F/ - reject
^https?://fdfs.xmcdn.com/group21/M0A/95/3B/ - reject
^https?://fdfs.xmcdn.com/group22/M00/92/FF/ - reject
^https?://fdfs.xmcdn.com/group22/M05/66/67/ - reject
^https?://fdfs.xmcdn.com/group22/M07/76/54/ - reject
^https?://fdfs.xmcdn.com/group23/M01/63/F1/ - reject
^https?://fdfs.xmcdn.com/group23/M04/E5/F6/ - reject
^https?://fdfs.xmcdn.com/group23/M07/81/F6/ - reject
^https?://fdfs.xmcdn.com/group23/M0A/75/AA/ - reject
^https?://fdfs.xmcdn.com/group24/M03/E6/09/ - reject
^https?://fdfs.xmcdn.com/group24/M07/C4/3D/ - reject
^https?://fdfs.xmcdn.com/group25/M05/92/D1/ - reject

// Zhangyue
^https?://book.img.ireader.com/group6/M00 - reject

// Youdao
^https?://dict.youdao.com/infoline/style\?client= - reject
^https?://gorgon.youdao.com/gorgon/request.s\?v= - reject
^https?://impservice.dictapp.youdao.com/imp/request.s\?req= - reject

// Youdao Translation
^https?://dict.youdao.com/infoline/style\?client=translator&apiversion=3.0&lastId=0&style=fanyiguantuijian - reject
^https?://oimagec2.ydstatic.com/image\?id=.+&product=adpublish - reject

// Yiche
^https?://api.ycapp.yiche.com/appnews/getadlist - reject
^https?://api.ycapp.yiche.com/yicheapp/getadlist - reject
^https?://api.ycapp.yiche.com/yicheapp/getappads/ - reject
^https?://cheyouapi.ycapp.yiche.com/appforum/getusermessagecount - reject

// Youtube
^https?://m.youtube.com/_get_ads - reject
^https?://pagead2.googlesyndication.com/pagead/ - reject
^https?://s0.2mdn.net/ads/ - reject
^https?://stats.tubemogul.com/stats/ - reject

// 0013
^https?://.+0013.+/upload/activity/app_flash_screen_ - reject

// Tianshan live
^http?://www.tsytv.com.cn/api/app/ios/ads - reject

// KFC
^https?://res.kfc.com.cn/advertisement/ - reject

// Shouyue auto
^https?://img.yun.01zhuanche.com/statics/app/advertisement/.+-750-1334 - reject

// Shenzhou auto
^https?://img01.10101111cdn.com/adpos/share/ - reject

// Flow cbb
^https?://bank.wo.cn/v9/getstartpage - reject

// Haiyan
^https?://img.ihytv.com/material/adv/img/ - reject

// Meituan Waimai
^https?://p\d{1}.meituan.net/wmbanner/ - reject

// QQ Pim
^https?://mmgr.gtimg.com/gjsmall/qqpim/public/ios/splash/.+/\d{4}_\d{4} - reject

// Jiemian News
^https?://img.jiemian.com/ads/ - reject

// Auto home
^https?://adproxy.autohome.com.cn/AdvertiseService/ - reject
^https?://app2.autoimg.cn/appdfs/ - reject

// Qidiandushu
^https?://mage.if.qidian.com/Atom.axd/Api/Client/GetConfIOS - reject

// DangDang
^https?://img\d{2}.ddimg.cn/upload_img/.+/670x900 - reject
^https?://img\d{2}.ddimg.cn/upload_img/.+/750x1064 - reject
^https?://mapi.dangdang.com/index.php\?action=init&user_client=iphone - reject

// Guotai
^https?://dl.app.gtja.com/operation/config/startupConfig.json - reject

// Laifeng Live
^https?://api.laifeng.com/v1/start/ads - reject

// Douyin
^https?://aweme.snssdk.com/aweme/v1/screen/ad/ - reject

// Xiachufang
^https?://api.xiachufang.com/v2/ad/ - reject

// Facebook
^https?://connect.facebook.net/en_US/fbadnw.js - reject

// Kuaidi100
^https?://qzonestyle.gtimg.cn/qzone/biz/gdt/mob/sdk/ios/v2/ - reject
^https?://cdn.kuaidi100.com/images/open/appads - reject
^https?://p.kuaidi100.com/mobile/mainapi.do - reject

// MiFit
^https?://api-mifit.huami.com/.+/app/startpages.json - reject
^https?://api-mifit.huami.com/.+/soc/well/list/community - reject
^https?://api-mifit.huami.com/discovery/mi/discovery/homepage_ad - reject
^https?://api-mifit.huami.com/discovery/mi/discovery/sport_ad - reject
^https?://api-mifit-cn.huami.com/.+/app/startpages.json - reject
^https?://api-mifit-cn.huami.com/.+/soc/well/list/community - reject
^https?://api-mifit-cn.huami.com/discovery/mi/discovery/homepage_ad - reject
^https?://api-mifit-cn.huami.com/discovery/mi/discovery/sport_ad - reject
^https?://hm.xiaomi.com/.+/app/startpages.json - reject
^https?://hm.xiaomi.com/.+/soc/well/list/community - reject
^https?://hm.xiaomi.com/discovery/mi/discovery/homepage_ad - reject
^https?://hm.xiaomi.com/discovery/mi/discovery/sport_ad - reject

// Weico
^https?://.+/portal.php\?a=get_ads - reject
^https?://.+/portal.php\?c=duiba - reject
^https?://.+/portal.php\?a=get_coopen_ads - reject
^https?://weicoapi.weico.cc/img/ad/ - reject
^https?://.+/weico4ad/ad/ - reject

// StarFans
^https?://g.cdn.pengpengla.com/starfantuan/boot-screen-info/ - reject

// Discuz
^https?://discuz.gtimg.cn/cloud/scripts/discuz_tips.js - reject

// Guopan
^https?://sapi.guopan.cn/get_buildin_ad - reject

// Jiakaobaodian
^https?://789.kakamobi.cn/.+adver - reject
^https?://smart.789.image.mucang.cn/advert - reject

// Zhaoshang cbb
^https?://pic1cdn.cmbchina.com/appinitads/ - reject

// Cmblife
^https?://mlife.cmbchina.com/ClientFace(Service)?/getAdvertisement.json - reject
^https?://mlife.cmbchina.com/ClientFace(Service)?/preCacheAdvertise.json - reject

// 163 News
^https?://g1.163.com/madfeedback - reject
^https?://img1.126.net/.+dpi=6401136 - reject
^https?://img1.126.net/channel14/ - reject
^https?://nex.163.com/q - reject

// ElongClient
^http?://123.59.30.10/adv/advInfos - reject

// AiRav
^https?://bbs.airav.cc/data/.+.jpg - reject
^https?://image.airav.cc/AirADPic/.+.gif - reject
^https?://m.airav.cc/images/Mobile_popout_cn.gif - reject

// IfengNews
^https?://c1.ifengimg.com/.+_w1080_h1410.jpg - reject

// Tencent guanjia
^https?://mmgr.gtimg.com/gjsmall/qiantu/upload/ - reject

// Peanut
^https?://cmsapi.wifi8.com/v1/emptyAd/info - reject
^https?://cmsapi.wifi8.com/v2/adNew/config - reject

// Tencent live
^https?://bla.gtimg.com/qqlive/\d{6}.+.png - reject
^https?://lives.l.qq.com/livemsg\?sdtfrom= - reject
^https?://splashqqlive.gtimg.com/website/\d{6} - reject

// AppSo
^https?://sso.ifanr.com/jiong/IOS/appso/splash/ - reject

// Ydstatic
^https?://oimage\w\d.ydstatic.com/image\?.+=adpublish - reject

// Huasheng
^https?://cmsfile.wifi8.com/uploads/png/ - reject

// Baiduyun
^https?://issuecdn.baidupcs.com/issue/netdisk/guanggao/ - reject

// Lanrentingshu
^https?://118.178.214.118/yyting/advertclient/ClientAdvertList.action - reject
^https?://dapis.mting.info/yyting/advertclient/ClientAdvertList.action - reject

// 91 Porn
^https?://192.133.+.mp4$ - reject

// Panda TV
^https?://static.api.m.panda.tv/index.php\?method=clientconf\.firstscreen - reject

// Weihou
^https?://api.app.vhall.com/v5/000/webinar/launch - reject

// ttlrs
^https?://img.53site.com/Werewolf/AD/ - reject
^https?://werewolf.53site.com/Werewolf/.+/getAdvertise.php - reject

// Apple
^https?://a.applovin.com/.+/ad - reject

// Weiyi
^https?://kano.guahao.cn/.+\?resize=\w{3}-\w{4} - reject

// Chelaile
^https?://pic1.chelaile.net.cn/adv/ - reject

// Jiankang 160
^https?://images.91160.com/primary/ - reject

// 1qianbao
^https?://d.1qianbao.com/youqian/ads/ - reject

// HuomaoTV
^https?://api.huomao.com/channels/loginAd - reject

// QQ Misic
^https?://.+/music/common/upload/t_splash_info - reject
^https?://.+/tips/fcgi-bin/fcg_get_advert - reject
^https?://y.gtimg.cn/music/common/upload/targeted_ads - reject

// Taobao
^https?://gw.alicdn.com/imgextra/i\d/.+960x960.+ - reject

// Kuaikanmanhua
^https?://api.kkmh.com/v3/ad/show - reject
^https?://api.kkmh.com/v3/ad/upload - reject
^https?://api.kkmh.com/v1/advertisement/opening/show - reject

// Hupu
^https?://i1.hoopchina.com.cn/blogfile/.+_750x1334 - reject

// Other
^.+/ad(s|v)?.js - reject
^.+allOne.php\?ad_name=main_splash_ios - reject
^.+nga.cn.+\bhome.+\b=ad - reject
^.+resource=article\/recommend\&accessToken= - reject
^https?://113.200.76.*:16420/sxtd.bike2.01/getkey.do - reject
^https?://creatives.ftimg.net/ads/ - reject
^https?://dd.iask.cn/ddd/adAudit - reject
^https?://g.tbcdn.cn/mtb/ - reject
^https?://gorgon.youdao.com\/gorgon\/request\.s\?v= - reject
^https?://huichuan.sm.cn/jsad? - reject
^https?://impservice.youdao.com\/imp\/request\.s\?req= - reject
^https?://iphone265g.com/templates/iphone/bottomAd.js - reject
^https?://m.+.china.com.cn/statics/sdmobile/js/ad - reject
^https?://m.+.china.com.cn/statics/sdmobile/js/mobile.advert.js - reject
^https?://m.+.china.com.cn/statics/sdmobile/js/mobileshare.js - reject
^https?://m.elecfans.com/static/js/ad.js - reject
^https?://mi.gdt.qq.com\/gdt_mview.fcg\?posid= - reject 
^https?://nga\.cn.+\bhome.+\b=ad - reject
^https?://player.hoge.cn/advertisement.swf - reject
^https?://ress.dxpmedia.com/appicast/ - reject
^https?://s1.api.tv.itc.cn/v4/mobile/feeling/list.json\?api_key= - reject
^https?://s3.pstatp.com/inapp/TTAdblock.css - reject
^https?://statc.mytuner.mobi/media/banners/ - reject
^https?://static.cnbetacdn.com/assets/adv - reject
^https?://static.iask.cn/m-v20161228/js/common/adAudit.min.js - reject
^https?://v.17173.com/api/Allyes/ - reject
^https?://wmedia-track.uc.cn - reject
^https?://www.ft.com/__origami/service/image/v2/images/raw/https%3A%2F%2Fcreatives.ftimg.net%2Fads* - reject
^https?://www.inoreader.com/adv/ - reject
^https?://www.lianbijr.com/adPage/ - reject



[Header Rewrite]
^*.qpic.cn header-replace User-Agent WeChat/6.5.22.32 CFNetwork/889.9 Darwin/17.2.0
^*.qpic.cn header-del Referer
^*.ph.126.net header-del Referer

[SSID Setting]


[MITM]
// hostname
// MITM';
    }

    private static function GetSurge($passwd, $method, $server, $port, $defined)
    {
        $rulelist = base64_decode(file_get_contents("https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt"))."\n".$defined;
        $gfwlist = explode("\n", $rulelist);

        $count = 0;
        $pac_content = '';
        $find_function_content = '
[General]
skip-proxy = 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12, localhost, *.local
bypass-tun = 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12
dns-server = 119.29.29.29, 223.5.5.5, 114.114.114.114
loglevel = notify

[Proxy]
Proxy = custom,'.$server.','.$port.','.$method.','.$passwd.','.Config::get('baseUrl').'/downloads/SSEncrypt.module

[Rule]
DOMAIN-KEYWORD,adsmogo,REJECT
DOMAIN-SUFFIX,acs86.com,REJECT
DOMAIN-SUFFIX,adcome.cn,REJECT
DOMAIN-SUFFIX,adinfuse.com,REJECT
DOMAIN-SUFFIX,admaster.com.cn,REJECT
DOMAIN-SUFFIX,admob.com,REJECT
DOMAIN-SUFFIX,adsage.cn,REJECT
DOMAIN-SUFFIX,adsage.com,REJECT
DOMAIN-SUFFIX,adsmogo.org,REJECT
DOMAIN-SUFFIX,ads.mobclix.com,REJECT
DOMAIN-SUFFIX,adview.cn,REJECT
DOMAIN-SUFFIX,adwhirl.com,REJECT
DOMAIN-SUFFIX,adwo.com,REJECT
DOMAIN-SUFFIX,appads.com,REJECT
DOMAIN-SUFFIX,domob.cn,REJECT
DOMAIN-SUFFIX,domob.com.cn,REJECT
DOMAIN-SUFFIX,domob.org,REJECT
DOMAIN-SUFFIX,doubleclick.net,REJECT
DOMAIN-SUFFIX,duomeng.cn,REJECT
DOMAIN-SUFFIX,duomeng.net,REJECT
DOMAIN-SUFFIX,duomeng.org,REJECT
DOMAIN-SUFFIX,googeadsserving.cn,REJECT
DOMAIN-SUFFIX,guomob.com,REJECT
DOMAIN-SUFFIX,immob.cn,REJECT
DOMAIN-SUFFIX,inmobi.com,REJECT
DOMAIN-SUFFIX,mobads.baidu.com,REJECT
DOMAIN-SUFFIX,mobads-logs.baidu.com,REJECT
DOMAIN-SUFFIX,smartadserver.com,REJECT
DOMAIN-SUFFIX,tapjoyads.com,REJECT
DOMAIN-SUFFIX,umeng.co,REJECT
DOMAIN-SUFFIX,umeng.com,REJECT
DOMAIN-SUFFIX,umtrack.com,REJECT
DOMAIN-SUFFIX,uyunad.com,REJECT
DOMAIN-SUFFIX,youmi.net,REJECT'."\n";
        $isget=array();
        foreach ($gfwlist as $index=>$rule) {
            if (empty($rule)) {
                continue;
            } elseif (substr($rule, 0, 1) == '!' || substr($rule, 0, 1) == '[') {
                continue;
            }

            if (substr($rule, 0, 2) == '@@') {
                // ||开头表示前面还有路径
                if (substr($rule, 2, 2) =='||') {
                    //$rule_reg = preg_match("/^((http|https):\/\/)?([^\/]+)/i",substr($rule, 2), $matches);
                    $host = substr($rule, 4);
                    //preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    $find_function_content.="DOMAIN,".$host.",DIRECT,force-remote-dns\n";
                    continue;
                // !开头相当于正则表达式^
                } elseif (substr($rule, 2, 1) == '|') {
                    preg_match("/(\d{1,3}\.){3}\d{1,3}/", substr($rule, 3), $matches);
                    if (!isset($matches[0])) {
                        continue;
                    }

                    $host = $matches[0];
                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        $find_function_content.="IP-CIDR,".$host."/32,DIRECT,no-resolve \n";
                        continue;
                    } else {
                        preg_match_all("~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i", substr($rule, 3), $matches);

                        if (!isset($matches[4][0])) {
                            continue;
                        }

                        $host = $matches[4][0];
                        if ($host != "") {
                            if (isset($isget[$host])) {
                                continue;
                            }
                            $isget[$host]=1;
                            $find_function_content.="DOMAIN-SUFFIX,".$host.",DIRECT,force-remote-dns\n";
                            continue;
                        }
                    }
                } elseif (substr($rule, 2, 1) == '.') {
                    $host = substr($rule, 3);
                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        $find_function_content.="DOMAIN-SUFFIX,".$host.",DIRECT,force-remote-dns \n";
                        continue;
                    }
                }
            }

            // ||开头表示前面还有路径
            if (substr($rule, 0, 2) =='||') {
                //$rule_reg = preg_match("/^((http|https):\/\/)?([^\/]+)/i",substr($rule, 2), $matches);
                $host = substr($rule, 2);
                //preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);

                if (strpos($host, "*")!==false) {
                    $host = substr($host, strpos($host, "*")+1);
                    if (strpos($host, ".")!==false) {
                        $host = substr($host, strpos($host, ".")+1);
                    }
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    $find_function_content.="DOMAIN-KEYWORD,".$host.",Proxy,force-remote-dns\n";
                    continue;
                }

                if (isset($isget[$host])) {
                    continue;
                }
                $isget[$host]=1;
                $find_function_content.="DOMAIN,".$host.",Proxy,force-remote-dns\n";
            // !开头相当于正则表达式^
            } elseif (substr($rule, 0, 1) == '|') {
                preg_match("/(\d{1,3}\.){3}\d{1,3}/", substr($rule, 1), $matches);

                if (!isset($matches[0])) {
                    continue;
                }

                $host = $matches[0];
                if ($host != "") {
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    $find_function_content.="IP-CIDR,".$host."/32,Proxy,no-resolve \n";
                    continue;
                } else {
                    preg_match_all("~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i", substr($rule, 1), $matches);

                    if (!isset($matches[4][0])) {
                        continue;
                    }

                    $host = $matches[4][0];
                    if (strpos($host, "*")!==false) {
                        $host = substr($host, strpos($host, "*")+1);
                        if (strpos($host, ".")!==false) {
                            $host = substr($host, strpos($host, ".")+1);
                        }
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        $find_function_content.="DOMAIN-KEYWORD,".$host.",Proxy,force-remote-dns\n";
                        continue;
                    }

                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        $find_function_content.="DOMAIN-SUFFIX,".$host.",Proxy,force-remote-dns\n";
                        continue;
                    }
                }
            } else {
                $host = substr($rule, 0);
                if (strpos($host, "/")!==false) {
                    $host = substr($host, 0, strpos($host, "/"));
                }

                if ($host != "") {
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    $find_function_content.="DOMAIN-KEYWORD,".$host.",PROXY,force-remote-dns \n";
                    continue;
                }
            }


            $count = $count + 1;
        }
        $find_function_content.='
DOMAIN-KEYWORD,google,Proxy,force-remote-dns
IP-CIDR,91.108.4.0/22,Proxy,no-resolve
IP-CIDR,91.108.56.0/22,Proxy,no-resolve
IP-CIDR,109.239.140.0/24,Proxy,no-resolve
IP-CIDR,149.154.160.0/20,Proxy,no-resolve
IP-CIDR,10.0.0.0/8,DIRECT
IP-CIDR,127.0.0.0/8,DIRECT
IP-CIDR,172.16.0.0/12,DIRECT
IP-CIDR,192.168.0.0/16,DIRECT
GEOIP,CN,DIRECT
FINAL,DIRECT
	  ';
        $pac_content.=$find_function_content;
        return $pac_content;
    }


    private static function GetSurgeGeo($passwd, $method, $server, $port)
    {
        return '
[General]

skip-proxy = 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12, localhost, *.local

bypass-tun = 192.168.0.0/16, 10.0.0.0/8, 172.16.0.0/12

dns-server = 119.29.29.29, 223.5.5.5, 114.114.114.114
loglevel = notify



[Proxy]

Proxy = custom,'.$server.','.$port.','.$method.','.$passwd.','.Config::get('baseUrl').'/downloads/SSEncrypt.module



[Rule]

DOMAIN-KEYWORD,adsmogo,REJECT

DOMAIN-SUFFIX,acs86.com,REJECT

DOMAIN-SUFFIX,adcome.cn,REJECT

DOMAIN-SUFFIX,adinfuse.com,REJECT

DOMAIN-SUFFIX,admaster.com.cn,REJECT

DOMAIN-SUFFIX,admob.com,REJECT

DOMAIN-SUFFIX,adsage.cn,REJECT

DOMAIN-SUFFIX,adsage.com,REJECT

DOMAIN-SUFFIX,adsmogo.org,REJECT

DOMAIN-SUFFIX,ads.mobclix.com,REJECT

DOMAIN-SUFFIX,adview.cn,REJECT

DOMAIN-SUFFIX,adwhirl.com,REJECT

DOMAIN-SUFFIX,adwo.com,REJECT

DOMAIN-SUFFIX,appads.com,REJECT

DOMAIN-SUFFIX,domob.cn,REJECT

DOMAIN-SUFFIX,domob.com.cn,REJECT

DOMAIN-SUFFIX,domob.org,REJECT

DOMAIN-SUFFIX,doubleclick.net,REJECT

DOMAIN-SUFFIX,duomeng.cn,REJECT

DOMAIN-SUFFIX,duomeng.net,REJECT

DOMAIN-SUFFIX,duomeng.org,REJECT

DOMAIN-SUFFIX,googeadsserving.cn,REJECT

DOMAIN-SUFFIX,guomob.com,REJECT

DOMAIN-SUFFIX,immob.cn,REJECT

DOMAIN-SUFFIX,inmobi.com,REJECT

DOMAIN-SUFFIX,mobads.baidu.com,REJECT

DOMAIN-SUFFIX,mobads-logs.baidu.com,REJECT

DOMAIN-SUFFIX,smartadserver.com,REJECT

DOMAIN-SUFFIX,tapjoyads.com,REJECT

DOMAIN-SUFFIX,umeng.co,REJECT

DOMAIN-SUFFIX,umeng.com,REJECT

DOMAIN-SUFFIX,umtrack.com,REJECT

DOMAIN-SUFFIX,uyunad.com,REJECT

DOMAIN-SUFFIX,youmi.net,REJECT

GEOIP,AD,Proxy
GEOIP,AE,Proxy
GEOIP,AF,Proxy
GEOIP,AG,Proxy
GEOIP,AI,Proxy
GEOIP,AL,Proxy
GEOIP,AM,Proxy
GEOIP,AO,Proxy
GEOIP,AQ,Proxy
GEOIP,AR,Proxy
GEOIP,AS,Proxy
GEOIP,AS,Proxy
GEOIP,AS,Proxy
GEOIP,AS,Proxy
GEOIP,AT,Proxy
GEOIP,AU,Proxy
GEOIP,AW,Proxy
GEOIP,AX,Proxy
GEOIP,AZ,Proxy
GEOIP,BA,Proxy
GEOIP,BD,Proxy
GEOIP,BE,Proxy
GEOIP,BF,Proxy
GEOIP,BG,Proxy
GEOIP,BH,Proxy
GEOIP,BI,Proxy
GEOIP,BJ,Proxy
GEOIP,BL,Proxy
GEOIP,BM,Proxy
GEOIP,BN,Proxy
GEOIP,BO,Proxy
GEOIP,BQ,Proxy
GEOIP,BR,Proxy
GEOIP,BS,Proxy
GEOIP,BT,Proxy
GEOIP,BW,Proxy
GEOIP,BY,Proxy
GEOIP,BZ,Proxy
GEOIP,CA,Proxy
GEOIP,CC,Proxy
GEOIP,CD,Proxy
GEOIP,CF,Proxy
GEOIP,CG,Proxy
GEOIP,CH,Proxy
GEOIP,CI,Proxy
GEOIP,CK,Proxy
GEOIP,CL,Proxy
GEOIP,CM,Proxy
GEOIP,CO,Proxy
GEOIP,CR,Proxy
GEOIP,CU,Proxy
GEOIP,CV,Proxy
GEOIP,CW,Proxy
GEOIP,CX,Proxy
GEOIP,CY,Proxy
GEOIP,CZ,Proxy
GEOIP,DE,Proxy
GEOIP,DJ,Proxy
GEOIP,DK,Proxy
GEOIP,DM,Proxy
GEOIP,DO,Proxy
GEOIP,DZ,Proxy
GEOIP,EC,Proxy
GEOIP,EE,Proxy
GEOIP,EG,Proxy
GEOIP,EG,Proxy
GEOIP,EH,Proxy
GEOIP,ER,Proxy
GEOIP,ES,Proxy
GEOIP,ET,Proxy
GEOIP,FI,Proxy
GEOIP,FJ,Proxy
GEOIP,FK,Proxy
GEOIP,FM,Proxy
GEOIP,FO,Proxy
GEOIP,FR,Proxy
GEOIP,GA,Proxy
GEOIP,GB,Proxy
GEOIP,GD,Proxy
GEOIP,GE,Proxy
GEOIP,GF,Proxy
GEOIP,GG,Proxy
GEOIP,GH,Proxy
GEOIP,GI,Proxy
GEOIP,GL,Proxy
GEOIP,GM,Proxy
GEOIP,GN,Proxy
GEOIP,GP,Proxy
GEOIP,GQ,Proxy
GEOIP,GR,Proxy
GEOIP,GS,Proxy
GEOIP,GT,Proxy
GEOIP,GU,Proxy
GEOIP,GW,Proxy
GEOIP,GY,Proxy
GEOIP,HK,Proxy
GEOIP,HM,Proxy
GEOIP,HN,Proxy
GEOIP,HR,Proxy
GEOIP,HT,Proxy
GEOIP,HU,Proxy
GEOIP,ID,Proxy
GEOIP,IE,Proxy
GEOIP,IL,Proxy
GEOIP,IM,Proxy
GEOIP,IN,Proxy
GEOIP,IO,Proxy
GEOIP,IQ,Proxy
GEOIP,IR,Proxy
GEOIP,IS,Proxy
GEOIP,IT,Proxy
GEOIP,JE,Proxy
GEOIP,JM,Proxy
GEOIP,JO,Proxy
GEOIP,JP,Proxy
GEOIP,KE,Proxy
GEOIP,KG,Proxy
GEOIP,KH,Proxy
GEOIP,KI,Proxy
GEOIP,KM,Proxy
GEOIP,KN,Proxy
GEOIP,KP,Proxy
GEOIP,KR,Proxy
GEOIP,KW,Proxy
GEOIP,KY,Proxy
GEOIP,KZ,Proxy
GEOIP,LA,Proxy
GEOIP,LB,Proxy
GEOIP,LC,Proxy
GEOIP,LI,Proxy
GEOIP,LK,Proxy
GEOIP,LR,Proxy
GEOIP,LS,Proxy
GEOIP,LT,Proxy
GEOIP,LU,Proxy
GEOIP,LV,Proxy
GEOIP,LY,Proxy
GEOIP,MA,Proxy
GEOIP,MC,Proxy
GEOIP,MD,Proxy
GEOIP,ME,Proxy
GEOIP,MF,Proxy
GEOIP,MG,Proxy
GEOIP,MH,Proxy
GEOIP,MK,Proxy
GEOIP,ML,Proxy
GEOIP,MM,Proxy
GEOIP,MN,Proxy
GEOIP,MO,Proxy
GEOIP,MP,Proxy
GEOIP,MQ,Proxy
GEOIP,MR,Proxy
GEOIP,MS,Proxy
GEOIP,MT,Proxy
GEOIP,MU,Proxy
GEOIP,MV,Proxy
GEOIP,MW,Proxy
GEOIP,MX,Proxy
GEOIP,MY,Proxy
GEOIP,MZ,Proxy
GEOIP,NA,Proxy
GEOIP,NC,Proxy
GEOIP,NE,Proxy
GEOIP,NF,Proxy
GEOIP,NG,Proxy
GEOIP,NI,Proxy
GEOIP,NL,Proxy
GEOIP,NO,Proxy
GEOIP,NP,Proxy
GEOIP,NR,Proxy
GEOIP,NU,Proxy
GEOIP,NZ,Proxy
GEOIP,OM,Proxy
GEOIP,PA,Proxy
GEOIP,PE,Proxy
GEOIP,PF,Proxy
GEOIP,PG,Proxy
GEOIP,PH,Proxy
GEOIP,PK,Proxy
GEOIP,PL,Proxy
GEOIP,PM,Proxy
GEOIP,PN,Proxy
GEOIP,PR,Proxy
GEOIP,PS,Proxy
GEOIP,PT,Proxy
GEOIP,PW,Proxy
GEOIP,PY,Proxy
GEOIP,QA,Proxy
GEOIP,RE,Proxy
GEOIP,RO,Proxy
GEOIP,RS,Proxy
GEOIP,RU,Proxy
GEOIP,RW,Proxy
GEOIP,SA,Proxy
GEOIP,SB,Proxy
GEOIP,SC,Proxy
GEOIP,SD,Proxy
GEOIP,SE,Proxy
GEOIP,SG,Proxy
GEOIP,SH,Proxy
GEOIP,SI,Proxy
GEOIP,SJ,Proxy
GEOIP,SK,Proxy
GEOIP,SL,Proxy
GEOIP,SM,Proxy
GEOIP,SN,Proxy
GEOIP,SO,Proxy
GEOIP,SR,Proxy
GEOIP,SS,Proxy
GEOIP,ST,Proxy
GEOIP,SV,Proxy
GEOIP,SX,Proxy
GEOIP,SY,Proxy
GEOIP,SZ,Proxy
GEOIP,TC,Proxy
GEOIP,TD,Proxy
GEOIP,TF,Proxy
GEOIP,TG,Proxy
GEOIP,TH,Proxy
GEOIP,TJ,Proxy
GEOIP,TK,Proxy
GEOIP,TL,Proxy
GEOIP,TM,Proxy
GEOIP,TN,Proxy
GEOIP,TO,Proxy
GEOIP,TR,Proxy
GEOIP,TT,Proxy
GEOIP,TV,Proxy
GEOIP,TW,Proxy
GEOIP,TZ,Proxy
GEOIP,UA,Proxy
GEOIP,UG,Proxy
GEOIP,UM,Proxy
GEOIP,US,Proxy
GEOIP,UY,Proxy
GEOIP,UZ,Proxy
GEOIP,VA,Proxy
GEOIP,VC,Proxy
GEOIP,VE,Proxy
GEOIP,VG,Proxy
GEOIP,VI,Proxy
GEOIP,VN,Proxy
GEOIP,VU,Proxy
GEOIP,WF,Proxy
GEOIP,WS,Proxy
GEOIP,YE,Proxy
GEOIP,YT,Proxy
GEOIP,ZA,Proxy
GEOIP,ZM,Proxy
GEOIP,ZW,Proxy
IP-CIDR,91.108.4.0/22,Proxy,no-resolve

IP-CIDR,91.108.56.0/22,Proxy,no-resolve

IP-CIDR,109.239.140.0/24,Proxy,no-resolve

IP-CIDR,149.154.160.0/20,Proxy,no-resolve

IP-CIDR,10.0.0.0/8,DIRECT

IP-CIDR,127.0.0.0/8,DIRECT

IP-CIDR,172.16.0.0/12,DIRECT

IP-CIDR,192.168.0.0/16,DIRECT

GEOIP,CN,DIRECT

FINAL,Proxy';
    }

    private static function GetApn($apn, $server, $port)
    {
        return '
		<?xml version="1.0" encoding="UTF-8"?>
		<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
		<plist version="1.0">
		<dict>
			<key>PayloadContent</key>
			<array>
				<dict>
					<key>PayloadContent</key>
					<array>
						<dict>
							<key>DefaultsData</key>
							<dict>
								<key>apns</key>
								<array>
									<dict>
										<key>apn</key>
										<string>'.$apn.'</string>
										<key>proxy</key>
										<string>'.$server.'</string>
										<key>proxyPort</key>
										<integer>'.$port.'</integer>
									</dict>
								</array>
							</dict>
							<key>DefaultsDomainName</key>
							<string>com.apple.managedCarrier</string>
						</dict>
					</array>
					<key>PayloadDescription</key>
					<string>提供对营运商“接入点名称”的自定义。</string>
					<key>PayloadDisplayName</key>
					<string>APN</string>
					<key>PayloadIdentifier</key>
					<string>com.tony.APNUNI'.$server.'.</string>
					<key>PayloadOrganization</key>
					<string>Tony</string>
					<key>PayloadType</key>
					<string>com.apple.apn.managed</string>
					<key>PayloadUUID</key>
					<string>7AC1FC00-7670-41CA-9EE1-4A5882DBD'.rand(100, 999).'D</string>
					<key>PayloadVersion</key>
					<integer>1</integer>
				</dict>
			</array>
			<key>PayloadDescription</key>
			<string>APN配置文件</string>
			<key>PayloadDisplayName</key>
			<string>APN快速配置 - '.$server.' ('.$apn.')</string>
			<key>PayloadIdentifier</key>
			<string>com.tony.APNUNI'.$server.'</string>
			<key>PayloadOrganization</key>
			<string>Tony</string>
			<key>PayloadRemovalDisallowed</key>
			<false/>
			<key>PayloadType</key>
			<string>Configuration</string>
			<key>PayloadUUID</key>
			<string>4C355D66-E72E-4DC8-864F-62C416015'.rand(100, 999).'D</string>
			<key>PayloadVersion</key>
			<integer>1</integer>
		</dict>
		</plist>
		';
    }


    private static function GetPac($type, $address, $port, $defined)
    {
        header('Content-type: application/x-ns-proxy-autoconfig; charset=utf-8');
        return LinkController::get_pac($type, $address, $port, true, $defined);
    }

    private static function GetMacPac()
    {
        header('Content-type: application/x-ns-proxy-autoconfig; charset=utf-8');
        return LinkController::get_mac_pac();
    }


    private static function GetAcl($user)
    {
        $rulelist = base64_decode(file_get_contents("https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt"))."\n".$user->pac;
        $gfwlist = explode("\n", $rulelist);

        $count = 0;
        $acl_content = '';
        $find_function_content = '
#Generated by sspanel-glzjin-mod v3
#Time:'.date('Y-m-d H:i:s').'

[bypass_all]

';

        $proxy_list = '[proxy_list]

';
        $bypass_list = '[bypass_list]

';
        $outbound_block_list = '[outbound_block_list]

';

        $isget=array();
        foreach ($gfwlist as $index=>$rule) {
            if (empty($rule)) {
                continue;
            } elseif (substr($rule, 0, 1) == '!' || substr($rule, 0, 1) == '[') {
                continue;
            }

            if (substr($rule, 0, 2) == '@@') {
                // ||开头表示前面还有路径
                if (substr($rule, 2, 2) =='||') {
                    //$rule_reg = preg_match("/^((http|https):\/\/)?([^\/]+)/i",substr($rule, 2), $matches);
                    $host = substr($rule, 4);
                    //preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    //$find_function_content.="DOMAIN,".$host.",DIRECT,force-remote-dns\n";
                    $bypass_list .= $host."\n";
                    continue;
                // !开头相当于正则表达式^
                } elseif (substr($rule, 2, 1) == '|') {
                    preg_match("/(\d{1,3}\.){3}\d{1,3}/", substr($rule, 3), $matches);
                    if (!isset($matches[0])) {
                        continue;
                    }

                    $host = $matches[0];
                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        //$find_function_content.="IP-CIDR,".$host."/32,DIRECT,no-resolve \n";
                        $bypass_list .= $host."/32\n";
                        continue;
                    } else {
                        preg_match_all("~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i", substr($rule, 3), $matches);

                        if (!isset($matches[4][0])) {
                            continue;
                        }

                        $host = $matches[4][0];
                        if ($host != "") {
                            if (isset($isget[$host])) {
                                continue;
                            }
                            $isget[$host]=1;
                            //$find_function_content.="DOMAIN-SUFFIX,".$host.",DIRECT,force-remote-dns\n";
                            $bypass_list .= $host."\n";
                            continue;
                        }
                    }
                } elseif (substr($rule, 2, 1) == '.') {
                    $host = substr($rule, 3);
                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        //$find_function_content.="DOMAIN-SUFFIX,".$host.",DIRECT,force-remote-dns \n";
                        $bypass_list .= $host."\n";
                        continue;
                    }
                }
            }

            // ||开头表示前面还有路径
            if (substr($rule, 0, 2) =='||') {
                //$rule_reg = preg_match("/^((http|https):\/\/)?([^\/]+)/i",substr($rule, 2), $matches);
                $host = substr($rule, 2);
                //preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);

                if (strpos($host, "*")!==false) {
                    $host = substr($host, strpos($host, "*")+1);
                    if (strpos($host, ".")!==false) {
                        $host = substr($host, strpos($host, ".")+1);
                    }
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    //$find_function_content.="DOMAIN-KEYWORD,".$host.",Proxy,force-remote-dns\n";
                    $proxy_list .= $host."\n";
                    continue;
                }

                if (isset($isget[$host])) {
                    continue;
                }
                $isget[$host]=1;
                //$find_function_content.="DOMAIN,".$host.",Proxy,force-remote-dns\n";
                $proxy_list .= $host."\n";
            // !开头相当于正则表达式^
            } elseif (substr($rule, 0, 1) == '|') {
                preg_match("/(\d{1,3}\.){3}\d{1,3}/", substr($rule, 1), $matches);

                if (!isset($matches[0])) {
                    continue;
                }

                $host = $matches[0];
                if ($host != "") {
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;

                    preg_match("/(\d{1,3}\.){3}\d{1,3}\/\d{1,2}/", substr($rule, 1), $matches_ips);

                    if (!isset($matches_ips[0])) {
                        $proxy_list .= $host."/32\n";
                    } else {
                        $host = $matches_ips[0];
                        $proxy_list .= $host."\n";
                    }

                    //$find_function_content.="IP-CIDR,".$host."/32,Proxy,no-resolve \n";

                    continue;
                } else {
                    preg_match_all("~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i", substr($rule, 1), $matches);

                    if (!isset($matches[4][0])) {
                        continue;
                    }

                    $host = $matches[4][0];
                    if (strpos($host, "*")!==false) {
                        $host = substr($host, strpos($host, "*")+1);
                        if (strpos($host, ".")!==false) {
                            $host = substr($host, strpos($host, ".")+1);
                        }
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        //$find_function_content.="DOMAIN-KEYWORD,".$host.",Proxy,force-remote-dns\n";
                        $proxy_list .= $host."\n";
                        continue;
                    }

                    if ($host != "") {
                        if (isset($isget[$host])) {
                            continue;
                        }
                        $isget[$host]=1;
                        //$find_function_content.="DOMAIN-SUFFIX,".$host.",Proxy,force-remote-dns\n";
                        $proxy_list .= $host."\n";
                        continue;
                    }
                }
            } else {
                $host = substr($rule, 0);
                if (strpos($host, "/")!==false) {
                    $host = substr($host, 0, strpos($host, "/"));
                }

                if ($host != "") {
                    if (isset($isget[$host])) {
                        continue;
                    }
                    $isget[$host]=1;
                    //$find_function_content.="DOMAIN-KEYWORD,".$host.",PROXY,force-remote-dns \n";
                    $proxy_list .= $host."\n";
                    continue;
                }
            }


            $count = $count + 1;
        }

        $acl_content .= $find_function_content."\n".$proxy_list."\n".$bypass_list."\n".$outbound_block_list;
        return $acl_content;
    }



    /**
     * This is a php implementation of autoproxy2pac
     */
    private static function reg_encode($str)
    {
        $tmp_str = $str;
        $tmp_str = str_replace('/', "\\/", $tmp_str);
        $tmp_str = str_replace('.', "\\.", $tmp_str);
        $tmp_str = str_replace(':', "\\:", $tmp_str);
        $tmp_str = str_replace('%', "\\%", $tmp_str);
        $tmp_str = str_replace('*', ".*", $tmp_str);
        $tmp_str = str_replace('-', "\\-", $tmp_str);
        $tmp_str = str_replace('&', "\\&", $tmp_str);
        $tmp_str = str_replace('?', "\\?", $tmp_str);
        $tmp_str = str_replace('+', "\\+", $tmp_str);

        return $tmp_str;
    }

    private static function get_pac($proxy_type, $proxy_host, $proxy_port, $proxy_google, $defined)
    {
        $rulelist = base64_decode(file_get_contents("https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt"))."\n".$defined;
        $gfwlist = explode("\n", $rulelist);
        if ($proxy_google == "true") {
            $gfwlist[] = ".google.com";
        }

        $count = 0;
        $pac_content = '';
        $find_function_content = 'function FindProxyForURL(url, host) { var PROXY = "'.$proxy_type.' '.$proxy_host.':'.$proxy_port.'; DIRECT"; var DEFAULT = "DIRECT";'."\n";
        foreach ($gfwlist as $index=>$rule) {
            if (empty($rule)) {
                continue;
            } elseif (substr($rule, 0, 1) == '!' || substr($rule, 0, 1) == '[') {
                continue;
            }
            $return_proxy = 'PROXY';
        // @@开头表示默认是直接访问
        if (substr($rule, 0, 2) == '@@') {
            $rule = substr($rule, 2);
            $return_proxy = "DEFAULT";
        }

        // ||开头表示前面还有路径
        if (substr($rule, 0, 2) =='||') {
            $rule_reg = "^[\\w\\-]+:\\/+(?!\\/)(?:[^\\/]+\\.)?".LinkController::reg_encode(substr($rule, 2));
        // !开头相当于正则表达式^
        } elseif (substr($rule, 0, 1) == '|') {
            $rule_reg = "^" . LinkController::reg_encode(substr($rule, 1));
        // 前后匹配的/表示精确匹配
        } elseif (substr($rule, 0, 1) == '/' && substr($rule, -1) == '/') {
            $rule_reg = substr($rule, 1, strlen($rule) - 2);
        } else {
            $rule_reg = LinkController::reg_encode($rule);
        }
        // 以|结尾，替换为$结尾
        if (preg_match("/\|$/i", $rule_reg)) {
            $rule_reg = substr($rule_reg, 0, strlen($rule_reg) - 1)."$";
        }
            $find_function_content.='if (/' . $rule_reg . '/i.test(url)) return '.$return_proxy.';'."\n";
            $count = $count + 1;
        }
        $find_function_content.='return DEFAULT;'."}";
        $pac_content.=$find_function_content;
        return $pac_content;
    }


    private static function get_mac_pac()
    {
        $rulelist = base64_decode(file_get_contents("https://raw.githubusercontent.com/gfwlist/gfwlist/master/gfwlist.txt"));
        $gfwlist = explode("\n", $rulelist);
        $gfwlist[] = ".google.com";

        $count = 0;
        $pac_content = '';
        $find_function_content = 'function FindProxyForURL(url, host) { var PROXY = "SOCKS5 127.0.0.1:1080; SOCKS 127.0.0.1:1080; DIRECT;"; var DEFAULT = "DIRECT";'."\n";
        foreach ($gfwlist as $index=>$rule) {
            if (empty($rule)) {
                continue;
            } elseif (substr($rule, 0, 1) == '!' || substr($rule, 0, 1) == '[') {
                continue;
            }
            $return_proxy = 'PROXY';
        // @@开头表示默认是直接访问
        if (substr($rule, 0, 2) == '@@') {
            $rule = substr($rule, 2);
            $return_proxy = "DEFAULT";
        }

        // ||开头表示前面还有路径
        if (substr($rule, 0, 2) =='||') {
            $rule_reg = "^[\\w\\-]+:\\/+(?!\\/)(?:[^\\/]+\\.)?".LinkController::reg_encode(substr($rule, 2));
        // !开头相当于正则表达式^
        } elseif (substr($rule, 0, 1) == '|') {
            $rule_reg = "^" . LinkController::reg_encode(substr($rule, 1));
        // 前后匹配的/表示精确匹配
        } elseif (substr($rule, 0, 1) == '/' && substr($rule, -1) == '/') {
            $rule_reg = substr($rule, 1, strlen($rule) - 2);
        } else {
            $rule_reg = LinkController::reg_encode($rule);
        }
        // 以|结尾，替换为$结尾
        if (preg_match("/\|$/i", $rule_reg)) {
            $rule_reg = substr($rule_reg, 0, strlen($rule_reg) - 1)."$";
        }
            $find_function_content.='if (/' . $rule_reg . '/i.test(url)) return '.$return_proxy.';'."\n";
            $count = $count + 1;
        }
        $find_function_content.='return DEFAULT;'."}";
        $pac_content.=$find_function_content;
        return $pac_content;
    }

    public static function GetRouter($user, $is_mu = 0, $is_ss = 0)
    {
        $bash = '#!/bin/sh'."\n";
        $bash .= 'export PATH=\'/opt/usr/sbin:/opt/usr/bin:/opt/sbin:/opt/bin:/usr/local/sbin:/usr/sbin:/usr/bin:/sbin:/bin\''."\n";
        $bash .= 'export LD_LIBRARY_PATH=/lib:/opt/lib'."\n";
        $bash .= 'nvram set ss_type='.($is_ss == 1 ? '0' : '1')."\n";

        $count = 0;

        $items = URL::getAllItems($user, $is_mu, $is_ss);
        foreach($items as $item) {
            if($is_ss == 0) {
                $bash .= 'nvram set rt_ss_name_x'.$count.'="'.$item['remark']."\"\n";
                $bash .= 'nvram set rt_ss_port_x'.$count.'='.$item['port']."\n";
                $bash .= 'nvram set rt_ss_password_x'.$count.'="'.$item['passwd']."\"\n";
                $bash .= 'nvram set rt_ss_server_x'.$count.'='.$item['address']."\n";
                $bash .= 'nvram set rt_ss_usage_x'.$count.'="'."-o ".$item['obfs']." -g ".$item['obfs_param']." -O ".$item['protocol']." -G ".$item['protocol_param']."\"\n";
                $bash .= 'nvram set rt_ss_method_x'.$count.'='.$item['method']."\n";
                $count += 1;
            }else{
                $bash .= 'nvram set rt_ss_name_x'.$count.'="'.$item['remark']."\"\n";
                $bash .= 'nvram set rt_ss_port_x'.$count.'='.$item['port']."\n";
                $bash .= 'nvram set rt_ss_password_x'.$count.'="'.$item['passwd']."\"\n";
                $bash .= 'nvram set rt_ss_server_x'.$count.'='.$item['address']."\n";
                $bash .= 'nvram set rt_ss_usage_x'.$count.'=""'."\n";
                $bash .= 'nvram set rt_ss_method_x'.$count.'='.$item['method']."\n";
                $count += 1;
            }
        }

        $bash .= "nvram set rt_ssnum_x=".$count."\n";

        return $bash;
    }

    public static function GetSSRSub($user, $mu = 0, $max = 0)
    {
        return Tools::base64_url_encode(URL::getAllUrl($user, $mu, 0, 1));
    }
}
