package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.PostVO;
	import org.aerial.config.Config;

	public class PostService extends AbstractService
	{
		public function PostService()
		{
			super("PostService", Config.SERVER_URL, PostVO);
		}
	}
}