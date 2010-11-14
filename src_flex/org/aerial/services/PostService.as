package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.PostVO;
	import org.aerial.config;

	public class PostService extends AbstractService
	{
		public function PostService()
		{
			super("PostService", IConfig(new Config()).AMF_GATEWAY, PostVO);
		}
	}
}