使用`file`命令发现 `disk_dump` 为磁盘镜像, 挂载后列出目录:

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

   `ext4magic disk_dump -R -a $(date -d "-5day" +%s)`

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
