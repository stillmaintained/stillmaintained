# Still Maintained?

[![Project Status](https://stillmaintained.com/stillmaintained/stillmaintained.png)](https://stillmaintained.com/stillmaintained/stillmaintained)

Finally a place to mark (or check) an open source project if abandoned or looking for a new maintainer.

Originally created by [@jkreeftmeijer] ([original announcement][1]), this project was ported to [CakePHP 3.0] (beta) by
[yours truly][@jadb] in what started as an experiment. I decided to release it as the, long overdue now, new version of
the site hoping it also helps people working with CakePHP.

## Roadmap

*(read: brain dump)*

- ca-ching, ca-ching, caching (Redis)
- tests, enough said
- continuous integration (Travis or CircleCI)
- improve API (aim for RESTful?)
- seed database for development (Faker)
- key indicators (last commit date, last owner/collaborator reply to issue, # of opened/closed issues/PRs, etc.)
- Bitbucket integration
- extensions (browser, package managers)
- Capistrano task to create `tmp/.env` from ERB template

## Contributing

[StillMaintained.com][2] needs your help to become awesome. If you have a great idea, please create an [issue][3]. It
would be better if you forked the project, implemented your idea and sent me [a pull request][4] too. Just sayin'.;))

## Getting up and running

To make it dead simple, a Vagrant box is included. Clone the repo:

1. Clone the repo:
```
$ git clone https://github.com/stillmaintained/stillmaintained
$ cd stillmaintained
```

2. Define your Github application credentials:
```
$ sudo $EDITOR tmp/.env
```

3. Setup local domain:
```
$ sudo echo '192.168.13.37 local.stillmaintained.com' >> /etc/hosts
```

4. Start the VM:
```
$ vagrant up
```

5. Check the website by going to http://local.stillmaintained.com. If you run into any issues, please [let us know][3]. :)

## Thanks

I'd like to thank:

- [Linode], sponsor. Reliable and well priced hosting.
- [@doriath88], for sticking around so long.
- [@jkreeftmeijer], for entrusting me with this project.
- [CakePHP contributors][contributors], [cake3][CakePHP 3.0] is dope!

There are more people to thank, those are just the main ones.


[1]:http://jeffkreeftmeijer.com/2010/finally-a-way-to-mark-your-github-project-as-abandoned/
[2]:http://stillmaintained.com
[3]:/stillmaintained/stillmaintained/issues
[4]:/stillmaintained/stillmaintained/pulls
[@jkreeftmeijer]:http://twitter.com/jkreeftmeijer
[CakePHP 3.0]:https://github.com/cakephp/cakephp
[@jadb]:http://twitter.com/jadb
[Linode]:http://linode.com
[@doriath88]:http://twitter.com/doriath88
[contributors]:https://github.com/cakephp/cakephp/graphs/contributors
