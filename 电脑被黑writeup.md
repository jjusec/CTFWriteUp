
- 战队信息

战队信息：JJU划水二队
战队：406546	
战队排名：338

# 解题情况

## 题目序号`1` 题目名称`签到`

第一次见这种比赛30分钟后自动放出`FLAG ：）`的题

> `flag{同舟共济扬帆起，乘风破浪万里航。}`



## 题目序号2 题目名称：`the_best_ctf_game`:

用任意一款HEX编辑器打开FLAG文件，发现FLAG字样，使用strings进行可见字符串过滤
```shell
archme@gentoo$ strings -1 flag
```
得到`flag{65e02f26-0d6e-463f-bc63-2df733e47fbe}`



## 题目序号3 题目名称：电脑被黑

解压文件，使用`file`命令发现 `disk_dump` 为磁盘镜像, 挂载后列出目录:

```shell
.
./.Trash-0
./.Trash-0/files
./.Trash-0/files/flag_has_been_removed
./.Trash-0/info
./.Trash-0/info/flag.txt.trashinfo
./.Trash-0/info/flag.txt.trashinfo.6JL7K0
./disk_dump
./lost+found
./misc01
./misc01/.DS_Store
./misc01/demo
./misc01/fakeflag.txt
./misc01/main.c
./misc01/untitled
./misc01/???.png
```

发现 `.Trash-0/info/flag.txt.trashinfo`，疑似被删除文件。

使用ext4magic恢复删除文件，命令：

   `sudo ext4magic disk_dump -M -d ./RECOVERDIR`

 被恢复的文件在`./RECOVERDIR/`内为 `./misc01/flag.txt` 为恢复文件，但此文件被demo加密，于是开始逆向demo, demo 未加壳，直接得到伪代码:
 ![PIC](https://raw.githubusercontent.com/jjusec/CTFWriteUp/master/%E6%8D%95%E8%8E%B7.PNG)
 
 
 根据逻辑写出解密脚本
 ```python
 $ cat exp.py
with open("flag.txt",'rb') as f:
    res=f.read()
v4=34
v5=0
flag=""
for tmp in res:
    tmp=((tmp^v4)-v5)&0xff
    flag=flag+chr(tmp)
    v4=v4+34
    v5=(v5+2)&0xF
print(flag)
 ```
 
运行`decrypt.py`得到`flag`:
```shell
zzh@gentoo$ python3 decrypt.py
flag{e5d7c4ed-b8f6-4417-8317-b809fc26c047}
```

## 题目序号8 题目名称 BD

尝试低解密指数攻击
从task.py中得到`n,e,c`

```
n = 86966590627372918010571457840724456774194080910694231109811773050866217415975647358784246153710824794652840306389428729923771431340699346354646708396564203957270393882105042714920060055401541794748437242707186192941546185666953574082803056612193004258064074902605834799171191314001030749992715155125694272289
e = 46867417013414476511855705167486515292101865210840925173161828985833867821644239088991107524584028941183216735115986313719966458608881689802377181633111389920813814350964315420422257050287517851213109465823444767895817372377616723406116946259672358254060231210263961445286931270444042869857616609048537240249

c = 37625098109081701774571613785279343908814425141123915351527903477451570893536663171806089364574293449414561630485312247061686191366669404389142347972565020570877175992098033759403318443705791866939363061966538210758611679849037990315161035649389943256526167843576617469134413191950908582922902210791377220066
```

运行脚本

```python
def rational_to_contfrac (x, y):
    ''' 
    Converts a rational x/y fraction into
    a list of partial quotients [a0, ..., an] 
    '''
    a = x//y
    if a * y == x:
        return [a]
    else:
        pquotients = rational_to_contfrac(y, x - a * y)
        pquotients.insert(0, a)
        return pquotients
def convergents_from_contfrac(frac):    
    '''
    computes the list of convergents
    using the list of partial quotients 
    '''
    convs = [];
    for i in range(len(frac)):
        convs.append(contfrac_to_rational(frac[0:i]))
    return convs

def contfrac_to_rational (frac):
    '''Converts a finite continued fraction [a0, ..., an]
     to an x/y rational.
     '''
    if len(frac) == 0:
        return (0,1)
    elif len(frac) == 1:
        return (frac[0], 1)
    else:
        remainder = frac[1:len(frac)]
        (num, denom) = contfrac_to_rational(remainder)
        # fraction is now frac[0] + 1/(num/denom), which is 
        # frac[0] + denom/num.
        return (frac[0] * num + denom, num)

def egcd(a,b):
    '''
    Extended Euclidean Algorithm
    returns x, y, gcd(a,b) such that ax + by = gcd(a,b)
    '''
    u, u1 = 1, 0
    v, v1 = 0, 1
    while b:
        q = a // b
        u, u1 = u1, u - q * u1
        v, v1 = v1, v - q * v1
        a, b = b, a - q * b
    return u, v, a

def gcd(a,b):
    '''
    2.8 times faster than egcd(a,b)[2]
    '''
    a,b=(b,a) if a<b else (a,b)
    while b:
        a,b=b,a%b
    return a

def modInverse(e,n):
    '''
    d such that de = 1 (mod n)
    e must be coprime to n
    this is assumed to be true
    '''
    return egcd(e,n)[0]%n

def totient(p,q):
    '''
    Calculates the totient of pq
    '''
    return (p-1)*(q-1)

def bitlength(x):
    '''
    Calculates the bitlength of x
    '''
    assert x >= 0
    n = 0
    while x > 0:
        n = n+1
        x = x>>1
    return n


def isqrt(n):
    '''
    Calculates the integer square root
    for arbitrary large nonnegative integers
    '''
    if n < 0:
        raise ValueError('square root not defined for negative numbers')

    if n == 0:
        return 0
    a, b = divmod(bitlength(n), 2)
    x = 2**(a+b)
    while True:
        y = (x + n//x)//2
        if y >= x:
            return x
        x = y


def is_perfect_square(n):
    '''
    If n is a perfect square it returns sqrt(n),

    otherwise returns -1
    '''
    h = n & 0xF; #last hexadecimal "digit"

    if h > 9:
        return -1 # return immediately in 6 cases out of 16.

    # Take advantage of Boolean short-circuit evaluation
    if ( h != 2 and h != 3 and h != 5 and h != 6 and h != 7 and h != 8 ):
        # take square root if you must
        t = isqrt(n)
        if t*t == n:
            return t
        else:
            return -1

    return -1

def hack_RSA(e,n):
    frac = rational_to_contfrac(e, n)
    convergents = convergents_from_contfrac(frac)

    for (k,d) in convergents:
        #check if d is actually the key
        if k!=0 and (e*d-1)%k == 0:
            phi = (e*d-1)//k
            s = n - phi + 1
            # check if the equation x^2 - s*x + n = 0
            # has integer roots
            discr = s*s - 4*n
            if(discr>=0):
                t = is_perfect_square(discr)
                if t!=-1 and (s+t)%2==0:
                    print("\nHacked!")
                    return d

def main():
    n = 86966590627372918010571457840724456774194080910694231109811773050866217415975647358784246153710824794652840306389428729923771431340699346354646708396564203957270393882105042714920060055401541794748437242707186192941546185666953574082803056612193004258064074902605834799171191314001030749992715155125694272289
    e = 46867417013414476511855705167486515292101865210840925173161828985833867821644239088991107524584028941183216735115986313719966458608881689802377181633111389920813814350964315420422257050287517851213109465823444767895817372377616723406116946259672358254060231210263961445286931270444042869857616609048537240249
    d=hack_RSA(e,n)
    print ("d=")
    print (d)

if __name__ == '__main__':
    main()
```


得到私钥
```
d=
1485313191830359055093545745451584299495272920840463008756233
```
进行解密`m = pow(c,d,n)`
得到十六进制明文，十六进制转换成明文得到flag:
```
flag{d3752538-90d0-c373-cfef-9247d3e16848}
```

## 题目编号19 题目名称easyphp



